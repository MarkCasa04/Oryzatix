import { createContext, useContext, useState, useEffect } from 'react';

const LanguageContext = createContext(null);

const STORAGE_KEY = 'oryzatix_ui_lang';

export function LanguageProvider({ children }) {
    const [lang, setLangState] = useState(() => localStorage.getItem(STORAGE_KEY) || 'english');

    useEffect(() => {
        localStorage.setItem(STORAGE_KEY, lang);
    }, [lang]);

    const setLang = (newLang) => setLangState(newLang);

    const t = (en, tl) => (lang === 'english' ? en : tl);

    return (
        <LanguageContext.Provider value={{ lang, setLang, t }}>
            {children}
        </LanguageContext.Provider>
    );
}

export function useLanguage() {
    const ctx = useContext(LanguageContext);
    if (!ctx) throw new Error('useLanguage must be used within LanguageProvider');
    return ctx;
}
