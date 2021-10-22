const configStart = require('./../init/config.js');
const content = require('./getContent.js');
const init = require('./../init/init.js');

content.getContent(configStart.setLinkList(configStart.params), init.iList, init.linkEnteredCount);