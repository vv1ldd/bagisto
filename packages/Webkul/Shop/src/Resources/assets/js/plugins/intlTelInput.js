import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/build/css/intlTelInput.css';

const ruCountries = {
    af: "Афганистан", al: "Албания", dz: "Алжир", as: "Американское Самоа", ad: "Андорра", ao: "Ангола", ai: "Ангилья", aq: "Антарктида", ag: "Антигуа и Барбуда", ar: "Аргентина", am: "Армения", aw: "Аруба", au: "Австралия", at: "Австрия", az: "Азербайджан", bs: "Багамы", bh: "Бахрейн", bd: "Бангладеш", bb: "Барбадос", by: "Беларусь", be: "Бельгия", bz: "Белиз", bj: "Бенин", bm: "Бермуды", bt: "Бутан", bo: "Боливия", ba: "Босния и Герцеговина", bw: "Ботсвана", br: "Бразилия", io: "Британская территория в Индийском океане", vg: "Виргинские острова (Великобритания)", bn: "Бруней", bg: "Болгария", bf: "Буркина-Фасо", bi: "Бурунди", kh: "Камбоджа", cm: "Камерун", ca: "Канада", cv: "Кабо-Верде", ky: "Каймановы острова", cf: "Центральноафриканская Республика", td: "Чад", cl: "Чили", cn: "Китай", cx: "Остров Рождества", cc: "Кокосовые острова", co: "Колумбия", km: "Коморские острова", cg: "Конго (Браззавиль)", cd: "Конго (Киншаса)", ck: "Острова Кука", cr: "Коста-Рика", ci: "Кот-д’Ивуар", hr: "Хорватия", cu: "Куба", cy: "Кипр", cz: "Чехия", dk: "Дания", dj: "Джибути", dm: "Доминика", do: "Доминиканская Республика", ec: "Эквадор", eg: "Египет", sv: "Сальвадор", gq: "Экваториальная Гвинея", er: "Эритрея", ee: "Эстония", et: "Эфиопия", fk: "Фолклендские острова", fo: "Фарерские острова", fj: "Фиджи", fi: "Финляндия", fr: "Франция", gf: "Французская Гвиана", pf: "Французская Полинезия", tf: "Французские Южные территории", ga: "Габон", gm: "Гамбия", ge: "Грузия", de: "Германия", gh: "Гана", gi: "Гибралтар", gr: "Греция", gl: "Гренландия", gd: "Гренада", gp: "Гваделупа", gu: "Гуам", gt: "Гватемала", gg: "Гернси", gn: "Гвинея", gw: "Гвинея-Бисау", gy: "Гайана", ht: "Гаити", hm: "Острова Херд и Макдональд", va: "Ватикан", hn: "Гондурас", hk: "Гонконг", hu: "Венгрия", is: "Исландия", in: "Индия", id: "Индонезия", ir: "Иран", iq: "Ирак", ie: "Ирландия", im: "Остров Мэн", il: "Израиль", it: "Италия", jm: "Ямайка", jp: "Япония", je: "Джерси", jo: "Иордания", kz: "Казахстан", ke: "Кения", ki: "Кирибати", kp: "Северная Корея", kr: "Южная Корея", kw: "Кувейт", kg: "Киргизия", la: "Лаос", lv: "Латвия", lb: "Ливан", ls: "Лесото", lr: "Либерия", ly: "Ливия", li: "Лихтенштейн", lt: "Литва", lu: "Люксембург", mo: "Макао", mk: "Северная Македония", mg: "Мадагаскар", mw: "Малави", my: "Малайзия", mv: "Мальдивы", ml: "Мали", mt: "Мальта", mh: "Маршалловы острова", mq: "Мартиника", mr: "Мавритания", mu: "Маврикий", yt: "Майотта", mx: "Мексика", fm: "Микронезия", md: "Молдова", mc: "Монако", mn: "Монголия", me: "Черногория", ms: "Монтсеррат", ma: "Марокко", mz: "Мозамбик", mm: "Мьянма", na: "Намибия", nr: "Науру", np: "Непал", nl: "Нидерланды", nc: "Новая Каледония", nz: "Новая Зеландия", ni: "Никарагуа", ne: "Нигер", ng: "Нигерия", nu: "Ниуэ", nf: "Остров Норфолк", mp: "Северные Марианские острова", no: "Норвегия", om: "Оман", pk: "Пакистан", pw: "Палау", ps: "Палестина", pa: "Панама", pg: "Папуа — Новая Гвинея", py: "Парагвай", pe: "Перу", ph: "Филиппины", pn: "Питкэрн", pl: "Польша", pt: "Португалия", pr: "Пуэрто-Рико", qa: "Катар", re: "Реюньон", ro: "Румыния", ru: "Россия", rw: "Руанда", bl: "Сен-Бартелеми", sh: "Остров Святой Елены", kn: "Сент-Китс и Невис", lc: "Сент-Люсия", mf: "Сен-Мартен", pm: "Сен-Пьер и Микелон", vc: "Сент-Винсент и Гренадины", ws: "Самоа", sm: "Сан-Марино", st: "Сан-Томе и Принсипи", sa: "Саудовская Аравия", sn: "Сенегал", rs: "Сербия", sc: "Сейшельские острова", sl: "Сьерра-Леоне", sg: "Сингапур", sk: "Словакия", si: "Словения", sb: "Соломоновы острова", so: "Сомали", za: "Южно-Африканская Республика", gs: "Южная Георгия и Южные Сандвичевы острова", ss: "Южный Судан", es: "Испания", lk: "Шри-Ланка", sd: "Судан", sr: "Суринам", sj: "Шпицберген и Ян-Майен", sz: "Эсватини", se: "Швеция", ch: "Швейцария", sy: "Сирия", tw: "Тайвань", tj: "Таджикистан", tz: "Танзания", th: "Таиланд", tl: "Тимор-Лесте", tg: "Того", tk: "Токелау", to: "Тонга", tt: "Тринидад и Тобаго", tn: "Тунис", tr: "Турция", tm: "Туркменистан", tc: "Теркс и Кайкос", tv: "Тувалу", ug: "Уганда", ua: "Украина", ae: "ОАЭ", gb: "Великобритания", us: "США", um: "Малые удаленные острова США", uy: "Уругвай", uz: "Узбекистан", vu: "Вануату", ve: "Венесуэла", vn: "Вьетнам", vi: "Виргинские острова (США)", wf: "Уоллис и Футуна", eh: "Западная Сахара", ye: "Йемен", zm: "Замбия", zw: "Зимбабве"
};

export default {
    install: (app) => {
        window.intlTelInput = intlTelInput;

        app.directive('phone', {
            mounted(el) {
                const utilsScriptVersion = "26.7.5";
                const iti = intlTelInput(el, {
                    initialCountry: "auto",
                    geoIpLookup: function (callback) {
                        fetch("https://ipapi.co/json")
                            .then(res => res.json())
                            .then(data => callback(data.country_code))
                            .catch(() => callback("ru"));
                    },
                    separateDialCode: true,
                    strictMode: true,
                    countrySearch: true,
                    utilsScript: `https://cdn.jsdelivr.net/npm/intl-tel-input@${utilsScriptVersion}/build/js/utils.js`,
                    preferredCountries: ["ru", "by", "kz", "uz", "ua"],
                    i18n: {
                        searchPlaceholder: "Поиск страны...",
                        noResults: "Результатов не найдено",
                        selectedCountryAriaLabel: "Выбранная страна",
                        searchPlaceholderAriaLabel: "Поиск страны",
                        countryListAriaLabel: "Список стран",
                        ...ruCountries
                    },
                });

                const updatePlaceholderAndMaxLength = () => {
                    const checkInterval = setInterval(() => {
                        if (typeof iti.getExampleNumber === 'function') {
                            clearInterval(checkInterval);

                            const selectedData = iti.getSelectedCountryData();
                            const dialCode = selectedData.dialCode || "";
                            const dialCodeLength = dialCode.replace(/\D/g, '').length;

                            // E.164 maximum is 15 digits TOTAL (including dial code)
                            const maxRemainingDigits = 15 - dialCodeLength;

                            const exampleNumber = iti.getExampleNumber();
                            if (exampleNumber) {
                                const digitsOnly = exampleNumber.replace(/\D/g, '');
                                // Use whichever is smaller: country-specific length or E.164 limit
                                const finalMaxLength = Math.min(digitsOnly.length, maxRemainingDigits);
                                el.setAttribute('maxlength', finalMaxLength.toString());
                            } else {
                                el.setAttribute('maxlength', maxRemainingDigits.toString());
                            }
                        }
                    }, 100);

                    setTimeout(() => clearInterval(checkInterval), 5000);
                };

                updatePlaceholderAndMaxLength();

                el.addEventListener('countrychange', updatePlaceholderAndMaxLength);

                let isComposing = false;
                el.addEventListener('compositionstart', () => { isComposing = true; });
                el.addEventListener('compositionend', (e) => {
                    isComposing = false;
                    el.dispatchEvent(new Event('input'));
                });

                el.addEventListener('keydown', (e) => {
                    if (isComposing) return;

                    const isControl = e.ctrlKey || e.metaKey || e.altKey;
                    const isAllowed = [
                        'Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight', 'Home', 'End'
                    ].includes(e.key);
                    const isDigit = /^\d$/.test(e.key);

                    if (!isDigit && !isAllowed && !isControl) {
                        e.preventDefault();
                    }
                });

                const cleanInput = (target) => {
                    const cursorPosition = target.selectionStart;
                    const originalLength = target.value.length;
                    let cleaned = target.value.replace(/\D/g, '');

                    // Enforce maxlength (calculated in updatePlaceholderAndMaxLength)
                    const maxLength = target.getAttribute('maxlength');
                    if (maxLength && cleaned.length > parseInt(maxLength)) {
                        cleaned = cleaned.substring(0, parseInt(maxLength));
                    }

                    if (target.value !== cleaned) {
                        target.value = cleaned;
                        target.dispatchEvent(new Event('input', { bubbles: true }));

                        const newLength = target.value.length;
                        if (cursorPosition !== null && originalLength !== newLength) {
                            const pos = Math.max(0, cursorPosition - (originalLength - newLength));
                            target.setSelectionRange(pos, pos);
                        }
                    }
                };

                el.addEventListener('input', (e) => {
                    if (isComposing) return;
                    cleanInput(e.target);
                });

                el.addEventListener('paste', (e) => {
                    setTimeout(() => cleanInput(el), 0);
                });

                el.addEventListener('blur', () => {
                    cleanInput(el);
                    if (iti.isValidNumber()) {
                        const fullNumber = iti.getNumber();
                        const digits = fullNumber.replace(/\D/g, '');
                        // If it starts with dial code, we might want to strip it? 
                        // But usually Bagisto stores just the digits.
                        // Let's stay consistent with what's entered.
                    }
                });
            }
        });
    },
};
