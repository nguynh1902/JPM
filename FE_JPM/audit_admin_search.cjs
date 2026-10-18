const fs = require('fs');
const path = require('path');

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

const adminDir = path.resolve('FE_JPM/src/views/admin');
const files = getFiles(adminDir);

console.log('Total files found:', files.length);

const results = [];

files.forEach(f => {
  const content = fs.readFileSync(f, 'utf8');
  const relPath = path.relative(adminDir, f);

  // Look for any input with v-model in template
  const templateMatch = content.match(/<template>([\s\S]*?)<\/template>/i);
  const template = templateMatch ? templateMatch[1] : '';

  // Find all inputs in template
  const inputMatches = [...template.matchAll(/<input[^>]+>/gi)].map(m => m[0]);
  const searchInputs = inputMatches.filter(inp => /search|filter|tim|mã|tên|query/i.test(inp));

  // Find reset buttons
  const resetButtons = [...template.matchAll(/<button[^>]*@click=[\"']([^\"']+)[\"'][^>]*>([\s\S]*?)<\/button>/gi)]
    .filter(m => /đặt lại|reset/i.test(m[2]))
    .map(m => ({ handler: m[1], text: m[2].trim() }));

  // Find computed properties
  const computedMatch = content.match(/computed:\s*\{([\s\S]*?)\n\s*\},?\s*(?:methods|watch|mounted|data)/i);
  const scriptMatch = content.match(/<script[\s\S]*?>([\s\S]*?)<\/script>/i);
  const script = scriptMatch ? scriptMatch[1] : '';

  // Check reset buttons
  const brokenResets = [];
  resetButtons.forEach(rb => {
    const fnName = rb.handler.split('(')[0].trim();
    // check if it's inline like "search = ''"
    if (fnName.includes('=')) return;
    const isDefined = new RegExp('(?:function\\s+' + fnName + '|' + fnName + '\\s*\\(|' + fnName + '\\s*=|' + fnName + ':)', 'i').test(script);
    if (!isDefined) {
      brokenResets.push(fnName);
    }
  });

  // Check unsafe toLowerCase calls in script
  const unsafeToLower = [];
  const toLowerMatches = [...script.matchAll(/([a-zA-Z0-9_\.\?\$\s]+)\.toLowerCase\(\)/g)];
  toLowerMatches.forEach(m => {
    const raw = m[1].trim();
    // Check if it accesses an object property directly without fallback, e.g. item.name, m.email, c.title
    if (raw.includes('.') && !raw.startsWith('String(') && !raw.includes('||') && !raw.includes('?.') && !raw.includes('this.') && !raw.includes('search') && !raw.includes('filter') && !raw.includes('query') && !raw.includes('key')) {
      unsafeToLower.push(raw);
    }
  });

  // Table v-for source
  const vForMatches = [...template.matchAll(/v-for=[\"']\([^)]+\)\s+in\s+([a-zA-Z0-9_\.]+)|v-for=[\"'][a-zA-Z0-9_]+\s+in\s+([a-zA-Z0-9_\.]+)[\"']/gi)]
    .map(m => m[1] || m[2]);

  results.push({
    file: relPath,
    searchInputs: searchInputs.map(i => i.replace(/\s+/g, ' ').substring(0, 120)),
    resetButtons,
    brokenResets,
    unsafeToLower: [...new Set(unsafeToLower)],
    vForSources: [...new Set(vForMatches)],
  });
});

fs.writeFileSync(
  path.resolve('scratch_audit_results.json'),
  JSON.stringify(results, null, 2),
  'utf8'
);

console.log('Audit completed. Summary:');
results.forEach(r => {
  if (r.searchInputs.length > 0 || r.brokenResets.length > 0 || r.unsafeToLower.length > 0) {
    console.log(`\n[${r.file}]`);
    console.log(`  Search inputs: ${r.searchInputs.length}`);
    if (r.brokenResets.length > 0) {
      console.log(`  >>> BROKEN RESET HANDLERS: ${r.brokenResets.join(', ')}`);
    }
    if (r.unsafeToLower.length > 0) {
      console.log(`  >>> UNSAFE toLowerCase: ${r.unsafeToLower.join(', ')}`);
    }
    if (r.vForSources.length > 0) {
      console.log(`  v-for lists: ${r.vForSources.join(', ')}`);
    }
  }
});
