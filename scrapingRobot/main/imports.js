// import all modules needed in getContent and export them in a single module so getContent only need to import 1 module 
const configStart = require("./../init/config.js");
const sitemap = require("./../page/sitemap.js");
const eval = require("./../page/evaluate.js");
const click = require("./../page/click.js");
const write = require("./../text/write.js");
const logs = require("./../text/logs.js");
const link = require("./../page/link.js");
const open = require("./../page/open.js");
const init = require("./../init/init.js");
const save = require("./../data/save.js");

module.exports = { configStart, sitemap, eval, click, write, logs, link, open, init, save };