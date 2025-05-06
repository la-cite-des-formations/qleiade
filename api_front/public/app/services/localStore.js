export default {
    getLs(key, page) {
        let ls = {};
        if (global.localStorage) {
            try {
                ls = JSON.parse(global.localStorage.getItem(page)) || {};
            } catch (e) {
                /*Ignore*/
                console.log("error get ls : ", e);
                return false;
            }
        }
        return ls[key];
    },

    setLs(key, page, value) {
        if (global.localStorage) {
            global.localStorage.setItem(
                page,
                JSON.stringify({
                    [key]: value
                })
            );
        }
    },
}
