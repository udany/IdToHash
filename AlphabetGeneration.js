function randomInt(max, min) {
    if (!min) min = 0;
    return Math.floor(Math.random() * (max - min)) + min;
}

function shuffle(array) {
    var currentIndex = array.length, temporaryValue, randomIndex;

    // While there remain elements to shuffle...
    while (0 !== currentIndex) {

        // Pick a remaining element...
        randomIndex = Math.floor(Math.random() * currentIndex);
        currentIndex -= 1;

        // And swap it with the current element.
        temporaryValue = array[currentIndex];
        array[currentIndex] = array[randomIndex];
        array[randomIndex] = temporaryValue;
    }

    return array;
}

var alphabetSampleData = '012354679';

var i, start, end;

start = ('A').charCodeAt(0);
end = ('Z').charCodeAt(0);
for (i = start; i <= end; i++) {
    alphabetSampleData += String.fromCharCode(i);
}

start = ('a').charCodeAt(0);
end = ('z').charCodeAt(0);
for (i = start; i <= end; i++) {
    alphabetSampleData += String.fromCharCode(i);
}

function generateAlphabet(size, notIn) {
    alphabetSampleData = shuffle(alphabetSampleData.split('')).join('');

    var alphabet = '';
    if (!notIn) notIn = '';

    var i = 0;

    while (alphabet.length < size && i < alphabetSampleData.length) {
        var c = alphabetSampleData[i];
        if (notIn.indexOf(c) === -1) {
            alphabet += c;
        }
        i++;
    }

    return alphabet;
}

/// Good to use prime numbers here, sum should be smaller or equal to 62 (A-Za-z0-9)
var alphabetSize = 31, nullAlphabetSize = 31;

var myAlphabet = generateAlphabet(alphabetSize);
var myNullAlphabet = generateAlphabet(nullAlphabetSize, myAlphabet);

prompt("Here's your alphabet", myAlphabet);
prompt("Here's your null alphabet", myNullAlphabet);