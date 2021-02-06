const babel = require('@babel/core');
const fs = require('fs');

const code = fs.readFileSync(0, 'utf-8');

const result = babel.transformSync(code, {
  filename: process.cwd() + '/example.js',
});

process.stdout.write(result.code);