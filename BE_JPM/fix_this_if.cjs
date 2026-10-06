const fs = require('fs');
const path = require('path');

const directoryPath = path.join(__dirname, '../FE_JPM/src/views/admin');

function scanFiles(dir) {
    let filesList = [];
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            filesList = filesList.concat(scanFiles(fullPath));
        } else if (fullPath.endsWith('.vue')) {
            filesList.push(fullPath);
        }
    }
    return filesList;
}

const files = scanFiles(directoryPath);

files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    let changed = false;

    // Pattern to fix: this.if (isEditMode.value && formData.value.id === item.id) { resetForm(); }\n      deleteMutation.mutate(item.id);
    const brokenRegex = /this\.if\s*\(\s*isEditMode\.value\s*&&\s*formData\.value\.id\s*===\s*item\.id\s*\)\s*\{\s*resetForm\(\);\s*\}(\r?\n\s*)deleteMutation\.mutate\(item\.id\);/g;
    
    if (content.match(brokenRegex)) {
        content = content.replace(brokenRegex, "if (this.isEditMode && this.formData.id === item.id) { this.resetForm(); }$1this.deleteMutation.mutate(item.id);");
        changed = true;
    }

    if (changed) {
        fs.writeFileSync(file, content, 'utf8');
        console.log('Fixed ' + path.basename(file));
    }
});
