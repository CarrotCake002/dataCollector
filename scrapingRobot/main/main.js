const configStart = require('./../init/config.js');
const content = require('./getContent.js');
const init = require('./../init/init.js');

const params = configStart.params;
content.getContent(configStart.setLinkList(params), init.iList, init.linkEnteredCount);