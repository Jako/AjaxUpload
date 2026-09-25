const fs = require('fs');
const path = require('path');
const { minify } = require('terser');
const sass = require('sass');
const postcss = require('postcss');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');

// Read package information dynamically from package.json
const packageJson = require('./package.json');
const version = packageJson.version;
const phpversion = packageJson.phpversion;
const modxversion = packageJson.modxversion;
const currentYear = new Date().getFullYear();
const startYear = parseInt(packageJson.startYear) || currentYear;
const yearRange = currentYear > startYear ? `${startYear}-${currentYear}` : `${startYear}`;
const dateStr = new Date().toISOString()
    .split('T')[0];

const copyrightRegex = new RegExp(`Copyright ${startYear}(-\\d{4})? by`, 'g');
const copyrightReplace = `Copyright ${yearRange} by`;
const apiRegex = new RegExp(`&copy; ${startYear}(-\\d{4})?`, 'g');
const apiReplace = `&copy; ${yearRange}`;
const banner = `/*!\n * ${packageJson.fullname} - ${packageJson.description}\n * Version: ${packageJson.version}\n * Build date: ${dateStr}\n */\n`;

let versionParts = version.split('-');
let versionNumber = versionParts[0];
let versionRelease = versionParts[1] || 'pl';
let versionFull = versionNumber + '-' + versionRelease;

// Helper: Replace string/regex in a file
function replaceInFile(filePath, replacements, message = 'file') {
    if (!fs.existsSync(filePath)) {
        console.warn(`⚠ File not found: ${filePath}`);
        return;
    }
    let content = String(fs.readFileSync(filePath, 'utf8'));
    replacements.forEach(({ search, replace }) => {
        content = content.replace(search, replace);
    });
    fs.writeFileSync(filePath, content, 'utf8');
    console.log(`✓ Updated ${message}: ${filePath}`);
}

// Helper: Ensure directory structure exists
function ensureDirExists(dirPath) {
    if (!fs.existsSync(dirPath)) {
        fs.mkdirSync(dirPath, { recursive: true });
    }
}

// Helper: Copy a file
function copyFile(src, dest) {
    const fullSource = path.resolve(__dirname, src);
    const fullTarget = path.resolve(__dirname, dest);
    if (!fs.existsSync(fullSource)) {
        console.warn(`⚠ Source file not found for copy: ${src}`);
        return;
    }
    const targetDir = path.dirname(fullTarget);
    ensureDirExists(targetDir);
    fs.copyFileSync(fullSource, fullTarget);
    console.log(`✓ Copied: ${src} -> ${dest}`);
}

// Helper: Copy folders recursively with an optional filter function
async function copyFolderRecursive(src, dest, filterFn = (fileName) => true) {
    if (!fs.existsSync(src)) {
        console.warn(`⚠ Source file not found for copy: ${src}`);
        return;
    }
    ensureDirExists(dest);
    const entries = fs.readdirSync(src, { withFileTypes: true });

    for (let entry of entries) {
        const srcPath = path.join(src, entry.name);
        const destPath = path.join(dest, entry.name);

        if (entry.isDirectory()) {
            await copyFolderRecursive(srcPath, destPath, filterFn);
        } else if (filterFn(entry.name)) {
            fs.copyFileSync(srcPath, destPath);
            console.log(`✓ Copied: ${srcPath} -> ${destPath}`);
        }
    }
}

// Helper function to compile scripts
async function compileScripts(files, dest, filename) {
    console.log('Compiling scripts...');
    let combinedCode = files.map(f => fs.readFileSync(f, 'utf8'))
        .join('\n');
    const minified = await minify(combinedCode, {
        mangle: true,
        compress: true,
        format: {
            comments: false,
        },
    });
    const finalCode = banner + minified.code;
    ensureDirExists(dest);
    filename = filename.replace(/(\.\w+)$/i, '.min$1');
    fs.writeFileSync(path.join(dest, filename), finalCode, 'utf8');
}

// Helper function to compile, autoprefix and minify Sass
async function compileSass(src, intermediate, dest, filename) {
    console.log('Compiling Sass & processing CSS...');
    const sassResult = sass.compile(src, { style: 'expanded' });
    ensureDirExists(intermediate);
    fs.writeFileSync(path.join(intermediate, filename), sassResult.css, 'utf8');
    const postcssResult = await postcss([
        autoprefixer(),
        cssnano({ preset: ['default', { discardComments: { removeAll: true } }] })
    ])
        .process(sassResult.css, { from: undefined });
    const finalCss = postcssResult.css + '\n' + banner;
    ensureDirExists(dest);
    filename = filename.replace(/(\.\w+)$/i, '.min$1');
    fs.writeFileSync(path.join(dest, filename), finalCss, 'utf8');
}

async function taskBump() {
    console.log(`Bump (with version ${versionFull} and daterange: ${yearRange})...`);
    const copyrightFiles = [
        'core/components/ajaxupload/model/ajaxupload/ajaxupload.class.php',
        'core/components/ajaxupload/src/AjaxUpload.php',
    ];
    copyrightFiles.forEach(file => {
        replaceInFile(file, [{
            search: copyrightRegex,
            replace: copyrightReplace
        }], 'copyright in');
    });
    replaceInFile('core/components/ajaxupload/src/AjaxUpload.php', [{
        search: /version = '\d+\.\d+\.\d+-?[0-9a-z]*'/ig,
        replace: `version = '${version}'`
    }], 'version in');
    const docFiles = [
        'zensical.toml',
    ];
    docFiles.forEach(file => {
        replaceInFile(file, [{
            search: apiRegex,
            replace: apiReplace
        }], 'daterange in');
    });
    replaceInFile('docs/index.md', [{
        search: /[*-] MODX Revolution \d.\d.*/g,
        replace: '* MODX Revolution ' + modxversion + '+'
    }, {
        search: /[*-] PHP (v)?\d.\d.*/g,
        replace: '* PHP ' + phpversion + '+'
    }], 'requirements in');
    replaceInFile('core/components/ajaxupload/composer.json', [{
        search: /"php": "\d.\d.*?"/g,
        replace: '"php": "' + phpversion + '"'
    }, {
        search: /"php": ">=\d.\d.*?"/g,
        replace: '"php": ">=' + phpversion + '"'
    }], 'requirements in');
    const buildFiles = [
        '_build/config.json',
        'core/components/ajaxupload/composer.json',
    ];
    buildFiles.forEach(file => {
        replaceInFile(file, [{
            search: /"version": "\d+\.\d+\.\d+-?[0-9a-z]*"/ig,
            replace: `"version": "${version}"`
        }], 'version in');
    });
}

async function taskScripts() {
    await compileScripts([
        'node_modules/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js',
        'node_modules/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js',
        'node_modules/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js',
        'node_modules/filepond/dist/filepond.js',
    ], 'assets/components/ajaxupload/js/web/', 'ajaxupload.js');
}

async function taskSass() {
    await compileSass(
        'source/sass/web/ajaxupload.scss',
        'source/css/mgr/',
        'assets/components/ajaxupload/css/web/',
        'ajaxupload.css',
    );
}

async function taskLexicon() {
    const srcDir = 'node_modules/filepond/locale';
    const destBaseDir = 'core/components/ajaxupload/lexicon';

    try {
        const files = fs.readdirSync(srcDir);
        const jsFiles = files.filter(file => file.endsWith('.js'));
        jsFiles.forEach(file => {
            const srcPath = path.join(srcDir, file);
            let content = fs.readFileSync(srcPath, 'utf8');
            content = content
                .replace('export default {\n',
                    '<?php\n' +
                    '/**\n' +
                    ' * Web Lexicon Entries for AjaxUpload\n' +
                    ' *\n' +
                    ' * @package ajaxupload\n' +
                    ' * @subpackage lexicon\n' +
                    ' */\n'
                )
                .replace(/'/g, '’')
                .replace(/ *(.*?):\s+([’`"])(.*?)\2,?\s*\n/g, '$$_lang[\'ajaxupload.$1\'] = \'$3\';\n')
                .replace(/ *};?\n?$/, '');
            const destDirname = path.basename(file, '.js')
                .replace(/(.*)[-_].*/, '\$1');
            const destDir = path.join(destBaseDir, destDirname);
            const destPath = path.join(destDir, 'web.inc.php');
            fs.mkdirSync(destDir, { recursive: true });
            fs.writeFileSync(destPath, content, 'utf8');

            console.log(`✓ Created: ${path.join(destDir, 'web.inc.php')}`);
        });

        console.log('✓ Lexicon build completed successfully.');
    } catch (error) {
        console.error('✗ Error building lexicon:', error);
    }
}

const action = process.argv[2];
if (action === 'bump') {
    taskBump();
} else if (action === 'scripts') {
    taskScripts();
} else if (action === 'sass') {
    taskSass();
} else if (action === 'lexicon') {
    taskLexicon();
} else {
    // Default: Beides ausführen
    taskBump()
        .then(() => taskScripts())
        .then(() => taskSass());
}

console.log('Done!');
