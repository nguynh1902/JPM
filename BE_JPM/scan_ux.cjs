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
let issues = [];

vueFiles.forEach(file => {
    const content = fs.readFileSync(file, 'utf8');
    const filename = path.basename(file);
    
    // 1. Check if delete clears the form if editing the same record
    if (content.includes('deleteMutation =') || content.includes('deleteMutation.mutate')) {
        const deleteMatches = content.match(/mutationFn: async \(([^)]+)\).*?queryClient.setQueryData/s);
        const hasFormClear = content.includes('resetForm()') || content.includes('isEditMode = false') || content.includes('isEditMode.value = false');
        if (!content.includes('if (formData.value.id === id)')) {
            // It might not clear the form when deleting the currently editing item
            issues.push({ file: filename, issue: 'Form state might not clear upon delete (Missing check for editing item)' });
        }
    }
    
    // 2. Check if Create resets the form properly
    if (content.includes('saveMutation =') || content.includes('saveData')) {
        if (!content.includes('resetForm()') && !content.includes('formData.value = {')) {
            issues.push({ file: filename, issue: 'Form might not reset after Create/Update' });
        }
    }
    
    // 3. Check for File Upload but no Remove
    if (content.includes('type="file"')) {
        if (!content.includes('remove') && !content.includes('xóa ảnh') && !content.includes('Xóa ảnh') && !content.includes('Xóa logo') && !content.includes('xóa logo')) {
            issues.push({ file: filename, issue: 'Has file upload but no remove button' });
        }
    }
    
    // 4. List sorting / jumping - Check if new items are appended with Date.now() ID
    if (content.includes('Date.now()') || content.includes('id: Date.now()')) {
        // If it's a real backend, we should use the backend's returned item, not Date.now()
        issues.push({ file: filename, issue: 'Uses Date.now() for ID instead of Backend response (Causes list jump/desync)' });
    }
    
    // 5. Pagination - check if pagination logic exists
    if (!content.includes('pagination') && !content.includes('page') && content.includes('<table')) {
        issues.push({ file: filename, issue: 'Table exists but no visible pagination handling in frontend' });
    }

    // 6. Check for auto-code inputs
    if (content.match(/v-model="formData\.(ma_|code)/)) {
        if (!content.includes('readonly') && !content.includes(':disabled')) {
            issues.push({ file: filename, issue: 'Has input for Code/Ma but it is not readonly' });
        }
    }
});

console.log(JSON.stringify(issues, null, 2));
