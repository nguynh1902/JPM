const fs = require('fs');
const path = require('path');

const directoryPath = path.join(__dirname, 'app/Domain');

function scanFiles(dir) {
    let filesList = [];
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            filesList = filesList.concat(scanFiles(fullPath));
        } else if (fullPath.endsWith('.php')) {
            filesList.push(fullPath);
        }
    }
    return filesList;
}

const files = scanFiles(directoryPath);

files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    let changed = false;

    // Replace ClassName::all() with ClassName::orderBy('id', 'desc')->get()
    const pattern = /([A-Z][a-zA-Z0-9_]*)::all\(\)/g;
    if (content.match(pattern)) {
        content = content.replace(pattern, "$1::orderBy('id', 'desc')->get()");
        changed = true;
    }

    if (changed) {
        fs.writeFileSync(file, content, 'utf8');
        console.log('Fixed ' + path.basename(file));
    }
});
