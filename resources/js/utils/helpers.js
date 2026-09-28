export function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/[&<>"']/g, (c) => {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };
        return map[c] || c;
    });
}

export function detectLanguage(text) {
    const t = text.toLowerCase();
    if (/hello|hi|what|how|why|when|where|please|thank|yes|no|option|good|rice|disease|treatment|brown|spot|blast|blight|tungro|mildew|hispa|smut|healthy/.test(t)) {
        return 'english';
    }
    return 'tagalog';
}

export function languageLabel(lang) {
    if (lang === 'english') return 'English';
    return 'Tagalog';
}

export function roleLabel(role, lang = 'english') {
    const map = {
        english: { farmer: 'Rice Farmer', agri_worker: 'Agricultural Extension Worker', admin: 'Administrator' },
        tagalog: { farmer: 'Magsasaka ng Palay', agri_worker: 'Agricultural Extension Worker', admin: 'Administrator' },
    };
    return map[lang]?.[role] || map.english[role] || 'User';
}

export function getThumbClass(disease) {
    const lower = (disease || '').toLowerCase();
    if (lower.includes('healthy') || lower.includes('smut')) return 'healthy';
    if (lower.includes('blight') || lower.includes('blb') || lower.includes('brown') || lower.includes('downy') || lower.includes('hispa')) return 'blb';
    return 'blast';
}

export function getSeverityBadgeClass(severity) {
    if (severity === 'healthy') return 'mild';
    if (severity === 'moderate') return 'moderate';
    if (severity === 'severe') return 'severe';
    return 'mild';
}

/** Resolve storage/API image paths to a loadable browser URL */
export function resolveImageUrl(url) {
    if (!url) return null;
    if (url.startsWith('blob:') || url.startsWith('data:') || url.startsWith('http://') || url.startsWith('https://')) {
        return url;
    }
    const path = url.startsWith('/') ? url : `/${url}`;
    const { origin, pathname } = window.location;
    // XAMPP: http://localhost/Gregorio_Alaissa/public/...
    const publicIdx = pathname.indexOf('/public');
    if (publicIdx !== -1) {
        return `${origin}${pathname.slice(0, publicIdx + 7)}${path}`;
    }
    return `${origin}${path}`;
}
