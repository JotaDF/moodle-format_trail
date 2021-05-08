#!/usr/bin/env node

var fs = require('fs'),
    path = require('path'),
    util = require('util');

if (process.argv.length < 3) {
    process.exit(1);
}

// Retrieve the full module name
var fullmodname = process.argv[2];
var modname = fullmodname.split('-').pop();

var sourcefile = path.resolve(process.cwd(), '../../build',
        fullmodname, fullmodname + '-min.js');

var targetdir = path.resolve(process.cwd(), '../../', modname);

if (!fs.existsSync(targetdir)) {
    fs.mkdirSync(targetdir);
}

var targetfile = path.resolve(targetdir, modname + '.js');

var inputfile = fs.createReadStream(sourcefile),
    outputfile = fs.createWriteStream(targetfile);

inputfile.pipe(outputfile);
inputfile.on("end", function() {
  process.exit(0);
});
