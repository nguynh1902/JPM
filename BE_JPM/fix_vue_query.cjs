const fs = require('fs');
const path = require('path');

const directoryPath = path.join(__dirname, '../FE_JPM/src/views/admin');

function scanVueFiles(dir) {
    let filesList = [];
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            filesList = filesList.concat(scanVueFiles(fullPath));
        } else if (fullPath.endsWith('.vue')) {
            filesList.push(fullPath);
        }
    }
    return filesList;
}

const vueFiles = scanVueFiles(directoryPath);

vueFiles.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    let changed = false;

    // 1. Remove onMutate from useMutation
    // Match block: onMutate: async (...) => { ... },
    const onMutateRegex = /\s*onMutate:\s*async\s*\([^)]*\)\s*=>\s*\{[\s\S]*?(?=\s*(onError|onSuccess|onSettled):)/g;
    
    // We need a robust parsing or simpler regex to safely remove onMutate.
    // Actually, onMutate always returns { previousData }; and is followed by },
    
    // Let's replace the whole onMutate block by matching from `onMutate:` to `return { previousData };\n    },`
    const pattern1 = /\s*onMutate:\s*async\s*\([^)]*\)\s*=>\s*\{[\s\S]*?return\s*\{?\s*previousData\s*\}?;\s*\n\s*\},/g;
    
    if (content.match(pattern1)) {
        content = content.replace(pattern1, '');
        changed = true;
    }
    
    // 2. Clear context.previousData restoration from onError
    const pattern2 = /\s*queryClient\.setQueryData\(\[[^\]]+\]\,\s*context\.previousData\);/g;
    if (content.match(pattern2)) {
        content = content.replace(pattern2, '');
        changed = true;
    }
    
    // Remove "context" from onError parameters if unused? It's fine to leave it.
    
    // 3. Clear form on delete if it's currently editing
    // Look for confirmDelete = async (item) => { ... deleteMutation.mutate(item.id);
    const deleteMutatePattern = /(deleteMutation\.mutate\(item\.id\);)/g;
    if (content.match(deleteMutatePattern)) {
        const resetLogic = `if (isEditMode.value && formData.value.id === item.id) { resetForm(); }\n      $1`;
        if (!content.includes('if (isEditMode.value && formData.value.id === item.id)')) {
             content = content.replace(deleteMutatePattern, resetLogic);
             changed = true;
        }
    }

    if (changed) {
        fs.writeFileSync(file, content, 'utf8');
        console.log('Fixed ' + path.basename(file));
    }
});
