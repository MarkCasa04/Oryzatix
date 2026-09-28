import { useLanguage } from '../context/LanguageContext';

export default function LanguageSwitch({ compact = false }) {
    const { lang, setLang } = useLanguage();

    return (
        <div className="lang-switch" style={compact ? { background: 'rgba(255,255,255,0.12)' } : undefined}>
            <button type="button" className={lang === 'english' ? 'active' : ''} onClick={() => setLang('english')} style={compact ? { padding: '4px 10px', fontSize: 10 } : undefined}>
                EN
            </button>
            <button type="button" className={lang === 'tagalog' ? 'active' : ''} onClick={() => setLang('tagalog')} style={compact ? { padding: '4px 10px', fontSize: 10 } : undefined}>
                TL
            </button>
        </div>
    );
}
