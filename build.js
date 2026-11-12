const fs = require('fs').promises;
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
async function replaceInFile(filePath, replacements, message = 'file') {
    try {
        let content = await fs.readFile(filePath, 'utf8');
        replacements.forEach(({ search, replace }) => {
            content = content.replace(search, replace);
        });
        await fs.writeFile(filePath, content, 'utf8');
        console.log(`✓ Updated ${message}: ${filePath}`);
    } catch (e) {
        console.warn(`⚠ Error updating ${filePath}`);
    }
}

// Helper: Ensure directory structure exists
async function ensureDirExists(dirPath) {
    await fs.mkdir(path.dirname(dirPath), { recursive: true });
}

// Helper: Copy a file
async function copyFile(src, dest) {
    try {
        const fullSource = path.resolve(__dirname, src);
        const fullTarget = path.resolve(__dirname, dest);
        const targetDir = path.dirname(fullTarget);
        await ensureDirExists(targetDir);
        await fs.copyFile(fullSource, fullTarget);
        console.log(`✓ Copied: ${src} -> ${dest}`);
    } catch (e) {
        console.warn(`⚠ Error copying ${src} -> ${dest}`);
    }
}

// Helper: Copy folders recursively with an optional filter function
async function copyFolderRecursive(src, dest, filterFn = (fileName) => true) {
    await ensureDirExists(dest);
    const entries = await fs.readdir(src, { withFileTypes: true });
    for (let entry of entries) {
        const srcPath = path.join(src, entry.name);
        const destPath = path.join(dest, entry.name);

        if (entry.isDirectory()) {
            await copyFolderRecursive(srcPath, destPath, filterFn);
        } else if (filterFn(entry.name)) {
            try {
                await fs.copyFile(srcPath, destPath);
                console.log(`✓ Copied: ${srcPath} -> ${destPath}`);
            } catch (e) {
                console.warn(`⚠ Error copying ${srcPath} -> ${destPath}`);
            }
        }
    }
}

// Helper function to compile scripts
async function compileScripts(files, dest, filename) {
    console.log('Compiling scripts...');
    const fileContents = await Promise.all(
        files.map(f => fs.readFile(f, 'utf8'))
    );
    const minified = await minify(fileContents.join('\n'), {
        mangle: true,
        compress: true,
        format: {
            comments: false,
        },
    });
    const finalCode = banner + minified.code;
    await ensureDirExists(dest);
    filename = filename.replace(/(\.\w+)$/i, '.min$1');
    await fs.writeFile(path.join(dest, filename), finalCode, 'utf8');
}

// Helper function to compile, autoprefix and minify Sass
async function compileSass(files, intermediate, dest, filename) {
    console.log('Compiling Sass & processing CSS...');
    let concatenatedCss = '';
    for (const file of files) {
        const sassResult = sass.compile(file, { style: 'expanded' });
        const postcssResult = await postcss([
            autoprefixer()
        ])
            .process(sassResult.css, { from: undefined });
        concatenatedCss += postcssResult.css + '\n';
    }
    concatenatedCss = concatenatedCss.replace(/\/\*[\s\(\S\)]*?\*\/\n*/g, '');
    await ensureDirExists(intermediate);
    await fs.writeFile(path.join(intermediate, filename), concatenatedCss, 'utf8');
    const postcssResult = await postcss([
        autoprefixer(),
        cssnano({ preset: ['default', { discardComments: { removeAll: true } }] })
    ])
        .process(concatenatedCss, { from: undefined });
    const finalCss = postcssResult.css + '\n' + banner;
    await ensureDirExists(dest);
    filename = filename.replace(/(\.\w+)$/i, '.min$1');
    await fs.writeFile(path.join(dest, filename), finalCss, 'utf8');
}

async function taskBump() {
    console.log(`Bump (with version ${versionFull} and daterange: ${yearRange})...`);
    const copyrightFiles = [
        'core/components/ajaxupload/model/ajaxupload/ajaxupload.class.php',
        'core/components/ajaxupload/src/AjaxUpload.php',
    ];
    for (const file of copyrightFiles) {
        await replaceInFile(file, [{
            search: copyrightRegex,
            replace: copyrightReplace
        }], 'copyright in');
    }
    await replaceInFile('core/components/ajaxupload/src/AjaxUpload.php', [{
        search: /version = '\d+\.\d+\.\d+-?[0-9a-z]*'/ig,
        replace: `version = '${version}'`
    }], 'version in');
    const docFiles = [
        'zensical.toml',
    ];
    for (const file of docFiles) {
        await replaceInFile(file, [{
            search: apiRegex,
            replace: apiReplace
        }], 'daterange in');
    }
    await replaceInFile('docs/index.md', [{
        search: /[*-] MODX Revolution \d.\d.*/g,
        replace: '* MODX Revolution ' + modxversion + '+'
    }, {
        search: /[*-] PHP (v)?\d.\d.*/g,
        replace: '* PHP ' + phpversion + '+'
    }], 'requirements in');
    await replaceInFile('core/components/ajaxupload/composer.json', [{
        search: /"php": "\d.\d.*?"/g,
        replace: '"php": "' + phpversion + '"'
    }, {
        search: /"php": ">=\d.\d.*?"/g,
        replace: '"php": ">=' + phpversion + '"'
    }], 'requirements in');
    await replaceInFile('_build/config.json', [{
        search: /("name": "php",\s*"version": ")>=?[^"]*(")/g,
        replace: '$1>=' + phpversion + '$2'
    }, {
        search: /("name": "modx",\s*"version": ")>=?[^"]*(")/g,
        replace: '$1>=' + modxversion + '$2'
    }], 'requirements in');
    const buildFiles = [
        '_build/config.json',
        'core/components/ajaxupload/composer.json',
    ];
    for (const file of buildFiles) {
        await replaceInFile(file, [{
            search: /"version": "\d+\.\d+\.\d+-?[0-9a-z]*"/ig,
            replace: `"version": "${version}"`
        }], 'version in');
    }
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
    await compileSass([
        'source/sass/web/ajaxupload.scss',
    ], 'source/css/mgr/', 'assets/components/ajaxupload/css/web/', 'ajaxupload.css',);
}

async function taskLexicon() {
    const srcDir = 'node_modules/filepond/locale';
    const destBaseDir = 'core/components/ajaxupload/lexicon';
    // FilePond languages, that are not created as lexicon
    const skipLanguages = ['cy', 'kur', 'lus', 'sl', 'sr', 'ur'];

    try {
        const files = await fs.readdir(srcDir);
        const jsFiles = files.filter(file => file.endsWith('.js')).sort();
        for (const file of jsFiles) {
            const destDirname = path.basename(file, '.js')
                .replace(/(.*)[-_].*/, '\$1');
            if (skipLanguages.includes(destDirname)) {
                console.log(`- Skipped: ${file}`);
                continue;
            }
            const srcPath = path.join(srcDir, file);
            let content = await fs.readFile(srcPath, 'utf8');
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
            const destDir = path.join(destBaseDir, destDirname);
            const destPath = path.join(destDir, 'web.inc.php');
            await fs.mkdir(destDir, { recursive: true });
            await fs.writeFile(destPath, content, 'utf8');

            console.log(`✓ Created: ${destPath}`);
        }

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
