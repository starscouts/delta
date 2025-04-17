const xml2js = require('xml2js');

async function translate(text) {
    console.log(text);
    return await require('./translate.js')(text, language, translateTo);
}

async function replace(item) {
    if (item instanceof Array) {
        let list = [];

        for (let i of item) {
            if (typeof i === "string") {
                list.push(await translate(i));
            } else if (typeof i === "object") {
                list.push(dict_reverse(await replace(i)));
            } else {
                list.push(i);
            }
        }

        return list;
    } else if (typeof item === "object") {
        if (item["_"]) {
            item["_"] = await translate(item["_"]);
        }

        for (let key of Object.keys(item)) {
            if (key === "_" || key === "$") continue;
            item[key] = await replace(item[key]);
        }

        return item;
    } else {
        return item;
    }
}

function dict_reverse(obj) {
    new_obj= {}
    rev_obj = Object.keys(obj).reverse();
    rev_obj.forEach(function(i) {
        new_obj[i] = obj[i];
    })
    return new_obj;
}

module.exports = async (str, source, target) => {
    global.language = source;
    global.translateTo = target;
    global.parsed = await xml2js.parseStringPromise(str, {
        preserveChildrenOrder: true,
        tagNameProcessors: [
            (name) => {
                return name + "---" + require('crypto').randomBytes(4).toString("hex");
            }
        ]
    });

    await replace(parsed);

    const builder = new xml2js.Builder({ headless: true });
    return builder.buildObject(parsed);
}