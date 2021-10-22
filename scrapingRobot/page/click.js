// click items the user selected
async function clickItems(clickItems, page) {
    if (clickItems !== null && clickItems !== undefined && clickItems !== '') {
        await page.evaluate((clickItems) => {
            for (var i = 0; i < clickItems.length; i++) {
                document.querySelectorAll(clickItems[i]).forEach(item => {
                    item.click();
                })
            }
        }, clickItems);
        await page.waitFor(2000);
    }
}

module.exports = { clickItems };