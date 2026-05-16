const fs = require('fs');
const html = fs.readFileSync('c:/xampp/htdocs/ITSI/app/Views/GlobalAdmin/respaldos.php', 'utf8');
const match = html.match(/<script>([\s\S]*?)<\/script>/);
if (match) {
    fs.writeFileSync('js_test.js', match[1]);
    console.log('Extracted js_test.js');
} else {
    console.log('No script tag found');
}
