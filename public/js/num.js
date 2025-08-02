var lang = {
    'minus': 'minus',
    'point': 'butun',
    'numberNames': [
        [
            '',
            'bir',
            'ikki',
            'uch',
            'to\'rt',
            'besh',
            'olti',
            'yetti',
            'sakkiz',
            'to\'qqiz',
        ],
        [
            '',
            'o\'n',
            'yigirma',
            'o\'ttiz',
            'qirq',
            'ellik',
            'oltmish',
            'yetmish',
            'sakson',
            'to\'qson'
        ],
        ['', '', 'yuz']
    ],
    'units': [
        '',
        'ming',
        'million',
        'milliard',
        'trillion',
        'kvadrillion',
        'kvintillion',
        'sekstillion',
        'septillion',
        'oktillion',
        'nonillion',
        'detsillion'
    ]
};

const numberToWordsUz = {};

numberToScalesUz = (num) => {
    let number = num.toString();
    let isMinusExists = false;
    if (checkIfExistsMinus(number)) {
        isMinusExists = true;
        number = number.replace('-', '');
    }
    const numberLength = number.length;
    const numberScales = Math.ceil(numberLength / 3);
    const numberLengthGoal = numberScales * 3;
    const lackOfDigits = numberLengthGoal - numberLength;
    const extendedNumber = "0".repeat(lackOfDigits) + number;
    let cutNumber = [];

    if (isMinusExists) {
        cutNumber.push("-");
    }

    for (let i = 0; i < extendedNumber.length; i += 3) {
        const digit1 = extendedNumber[i];
        const digit2 = extendedNumber[i + 1];
        const digit3 = extendedNumber[i + 2];
        const digits = digit3 + digit2 + digit1;
        cutNumber.push(digits);
    }
    return cutNumber.reverse();
};

checkIfExistsMinus = (number) => {
    return number[0] == "-" ? true : false;
};

convertScalesToWordsUz = (numberArr) => {
    convertedResult = "";
    let isMinus = false;
    if (numberArr[numberArr.length - 1] == "-") {
        isMinus = true;
        numberArr.splice(-1, 1);
    }
    numberArr.forEach((element, index) => {
        const digit1 = parseInt(element[0]);
        const digit2 = parseInt(element[1]);
        const digit3 = parseInt(element[2]);
        const unitName = lang.units[index];
        let hundredUnitName = "";
        let digit1text = "";
        let digit2text = "";
        let digit3text = "";
        if (digit1 === 0 && digit2 === 0 && digit3 === 0) {
            return;
        }
        digit1text = lang.numberNames[0][digit1];
        digit2text = lang.numberNames[1][digit2];
        if (digit3 !== 0) {
            hundredUnitName = lang.numberNames[2][2];
        }
        digit3text = lang.numberNames[0][digit3];

        const isunitName =
            index !== 0 && !(digit1 === 0 && digit2 === 0 && digit3 === 0);
        const scaleResult = `${digit3text} ${hundredUnitName} ${digit2text} ${digit1text} ${
            isunitName ? unitName : ""
        }`
            .replace(/\s+/g, " ")
            .trim();
        convertedResult = `${scaleResult} ${convertedResult}`;
    });
    if (isMinus) {
        convertedResult = `${lang.minus} ${convertedResult}`;
    }
    return convertedResult;
};

numberToWordsUz.convert = function (number) {
    number = number.toString();

    let beforedot = number;

    let afterdot = null;

    if (number.indexOf(".") !== -1) {
        [beforedot, afterdot] = number.split('.');
    }
    else if (number.indexOf(",") !== -1) {
        [beforedot, afterdot] = number.split(',');
    }

    let convertedResult;

    const beforedots = numberToScalesUz(beforedot)

    let beforedotConvert = convertScalesToWordsUz(beforedots);

    convertedResult = beforedotConvert.trim();

    if (afterdot !== null) {
        const afterdots = numberToScalesUz(afterdot);

        let afterdotConvert = convertScalesToWordsUz(afterdots);

        convertedResult = `${convertedResult} ${lang.point} ${afterdotConvert.trim()}`;
    }

    return convertedResult;
};

prettyInt = function (number) {
    return new Intl.NumberFormat().format(number)
}


const datePickerI18N = {
    days: ["Yakshanba", "Dushanba", "Seshanba", "Chorshanba", "Payshanba", "Juma", "Shanba", "Yakshanba"],
    daysShort: ["Yak", "Du", "Se", "Chor", "Pay", "Jum", "Shan", "Yak"],
    daysMin: ["Ya", "Du", "Se", "Chor", "Pa", "Ju", "Sha", "Ya"],
    months: ["Yanvar", "Fevral", "Mart", "Aprel", "May", "Iyun", "Iyul", "Avgust", "Sentabr", "Oktabr", "Noyabr", "Dekabr"],
    monthsShort: ["Yan", "Fev", "Mar", "Apr", "May", "Iyun", "Iyul", "Avg", "Sen", "Okt", "Noy", "Dek"],
    today: "Bugun",
}
