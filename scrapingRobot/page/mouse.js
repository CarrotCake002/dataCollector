// click items the user selected
async function clickItems(clickItems, page) {
    if (clickItems !== null && clickItems !== undefined && clickItems !== '') {
        for (var i = 0; i < clickItems.length; i++) {
            await page.evaluate((clickItems) => {
                document.querySelectorAll(clickItems).forEach(item => {
                    item.click();
                })
            }, clickItems[i]);
            await page.waitFor(5000);
        }
    }
}

async function wheelScroll(page, params) {
    for (var i = 0; i < 20; i++) {
        await page.mouse.wheel({ deltaY: 550 });
        await page.waitFor(250);
    }
}

module.exports = { clickItems, wheelScroll };