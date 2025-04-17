const fs = require('fs');
const translatte = require('translatte');

if (!fs.existsSync('../data/translator.json')) fs.writeFileSync('../data/translator.json', '{}');
const cache = require('../data/translator.json');

module.exports = async (text, source, target) => {
    if (text.trim() === "") return text;

    if (!cache[source]) cache[source] = {};
    if (!cache[source][text]) cache[source][text] = {};

    if (cache[source][text]) {
        if (cache[source][text][target]) {
            return cache[source][text][target];
        }
    }

    let success = false;
    let translated;

    while (!success) {
        try {
            translated = (await translatte(text, { from: source, to: target })).text;
            success = true;
        } catch (e) {
            success = false;
        }
    }

    cache[source][text][target] = translated;
    fs.writeFileSync('../data/translator.json', JSON.stringify(cache));

    return translated;
}