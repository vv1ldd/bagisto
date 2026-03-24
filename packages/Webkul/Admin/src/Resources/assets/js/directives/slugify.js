const cyrillicMap = {
    'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'e', 'ж': 'zh',
    'з': 'z', 'и': 'i', 'й': 'y', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o',
    'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'h', 'ц': 'ts',
    'ч': 'ch', 'ш': 'sh', 'щ': 'shch', 'ъ': '', 'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu',
    'я': 'ya'
};

function transliterateCyrillic(text) {
    return text.toString().replace(/[а-яё]/gi, match => cyrillicMap[match.toLowerCase()] || match);
}

let debounce = (func, wait) => {
    let timeout;

    return function (...args) {
        clearTimeout(timeout);

        timeout = setTimeout(() => func.apply(this, args), wait);
    };
};

export default {
    mounted(el, binding) {
        const handler = debounce(function (e) {
            let val = transliterateCyrillic(e.target.value);
            e.target.value = val
                .toLowerCase()
                .normalize("NFKD") // Normalize Unicode
                .replace(/[\u0300-\u036f]/g, "") // Remove combining diacritical marks
                .replace(/[^\p{L}\p{N}\s-]+/gu, "") // Remove all non-letter, non-number characters except spaces and dashes
                .replace(/\s+/g, "-") // Replace spaces with dashes
                .replace(/-+/g, "-") // Avoid multiple consecutive dashes
                .replace(/^-+|-+$/g, ""); // Trim leading and trailing dashes
        }, 300); // Debounce delay in ms

        el.addEventListener("input", handler);
    },
};
