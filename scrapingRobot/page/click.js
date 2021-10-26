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

module.exports = { clickItems };