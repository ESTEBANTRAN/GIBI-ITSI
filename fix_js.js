const fs = require('fs');
let js = fs.readFileSync('js_test.js', 'utf8');
js = js.replace(/<\?=[^>]+>/g, "'URL'");
fs.writeFileSync('js_test3.js', js);
console.log('Created js_test3.js');
