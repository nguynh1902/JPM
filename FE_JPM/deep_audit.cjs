const fs = require('fs');
const path = require('path');

const adminDir = path.resolve('FE_JPM/src/views/admin');

function getFiles(dir, ext = '.vue') {
  let results = [];
  const list = fs.readdirSync(dir);
  list.forEach(file => {
    file = path.join(dir, file);
    const stat = fs.statSync(file);
    if (stat && stat.isDirectory()) {
      results = results.concat(getFiles(file, ext));
    } else if (file.endsWith(ext)) {
      results.push(file);
    }
  });
  return results;
}

const files = getFiles(adminDir);

files.forEach(f => {
  const content = fs.readFileSync(f, 'utf8');
  const relPath = path.relative(adminDir, f);

  // Find inputs that look like search
  const searchInputMatches = [...content.matchAll(/<input[^>]+(?:placeholder=[\'\"][^\'\"]*(?:tìm|search|mã|tên)[^\'\"]*[\'\"]|v-model=[\'\"][^\'\"]*(?:search|filter|timKiem|query)[^\'\"]*[\'\"])[^>]*>/gi)];
  
  // Find all table rows
  const trVFor = [...content.matchAll(/<tr[^>]+v-for=[\'\"]([^\'\"]+)[\'\"]/gi)].map(m => m[1]);
  
  console.log(`\n================== ${relPath} ==================`);
  if (searchInputMatches.length === 0) {
    console.log('NO search input detected.');
  } else {
    searchInputMatches.forEach(m => {
      const vModel = m[0].match(/v-model=[\'\"]([^\'\"]+)[\'\"]/i);
      console.log(`SEARCH INPUT: v-model="${vModel ? vModel[1] : 'NONE'}" in tag: ${m[0].replace(/\s+/g, ' ').substring(0, 100)}`);
    });
  }
  console.log(`TABLE v-for:`, trVFor);
  
  // Check if search variable is referenced in computed or methods or watch
  if (searchInputMatches.length > 0) {
    searchInputMatches.forEach(m => {
      const vModel = m[0].match(/v-model=[\'\"]([^\'\"]+)[\'\"]/i);
      if (vModel) {
        const prop = vModel[1].split('.').pop();
        // check occurrences of prop in script
        const scriptMatch = content.match(/<script[\s\S]*?>([\s\S]*?)<\/script>/i);
        const script = scriptMatch ? scriptMatch[1] : '';
        const count = (script.match(new RegExp('\\b' + prop + '\\b', 'g')) || []).length;
        console.log(`  variable '${vModel[1]}' referenced in script: ${count} times`);
      }
    });
  }
});
