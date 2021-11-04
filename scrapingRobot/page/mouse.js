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

// function to simulate a user wheel scroll in the X and Y axis
async function wheelScroll(page, params) {
    if (params['scrollY']) {
        var scroll = params['scrollY'];
        for (var i = 0; i < scroll[0]; i++) {
            await page.mouse.wheel({ deltaY: scroll[1] });
            await page.waitFor(scroll[2]);
        }
    }
    if (params['scrollX']) {
        scroll = params['scrollX'];
        for (var i = 0; i < scroll[0]; i++) {
            await page.mouse.wheel({ deltaY: scroll[1] });
            await page.waitFor(scroll[2]);
        }
    }
}

module.exports = { clickItems, wheelScroll };