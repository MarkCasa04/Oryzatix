import { useNavigate, useLocation } from 'react-router-dom';
import { useLanguage } from '../context/LanguageContext';

export default function BottomNav({ active = 'home' }) {
    const navigate = useNavigate();
    const { t } = useLanguage();

    return (
        <div className="bottom-nav">
            <button type="button" className={`nav-item ${active === 'home' ? 'active' : ''}`} onClick={() => navigate('/home')}>
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" /></svg>
                <span>{t('Home', 'Home')}</span>
            </button>
            <button type="button" className="nav-item scan-btn" onClick={() => navigate('/scan')}>
                <div className="scan-circle">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                        <circle cx="12" cy="13" r="4" />
                    </svg>
                </div>
            </button>
            <button type="button" className={`nav-item ${active === 'profile' ? 'active' : ''}`} onClick={() => navigate('/profile')}>
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" /></svg>
                <span>{t('Profile', 'Profile')}</span>
            </button>
        </div>
    );
}

export function DesktopSidebar() {
    const navigate = useNavigate();
    const location = useLocation();
    const { t } = useLanguage();

    const items = [
        { path: '/home', label: t('Home', 'Home'), icon: 'M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z' },
        { path: '/scan', label: t('Disease Scan', 'Pag-scan ng Sakit'), icon: 'M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z' },
        { path: '/history', label: t('History', 'Kasaysayan'), icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
        { path: '/consultation', label: t('AI Consult', 'AI Konsulta'), icon: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z' },
        { path: '/profile', label: t('Profile', 'Profile'), icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
    ];

    const isActive = (path) => location.pathname === path || location.pathname.startsWith(path + '/');

    return (
        <aside className="desktop-sidebar">
            <div className="sidebar-logo">ORYZATIX</div>
            <p className="sidebar-tagline">{t('Rice Disease Detection System', 'Sistema ng Pagtukoy ng Sakit sa Palay')}</p>
            <nav>
                {items.map((item) => (
                    <button key={item.path} type="button" className={isActive(item.path) ? 'active' : ''} onClick={() => navigate(item.path)}>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                            <path d={item.icon} />
                        </svg>
                        {item.label}
                    </button>
                ))}
            </nav>
            <div className="sidebar-footer">ORYZATIX v1.0 · BSIT JPC</div>
        </aside>
    );
}
