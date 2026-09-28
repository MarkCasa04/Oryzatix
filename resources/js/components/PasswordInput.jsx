import { useState } from 'react';
import { useLanguage } from '../context/LanguageContext';

export default function PasswordInput({
    id,
    name,
    value,
    onChange,
    placeholder = '••••••••',
    minLength,
    required = false,
    autoComplete,
}) {
    const { t } = useLanguage();
    const [visible, setVisible] = useState(false);

    return (
        <div className="password-input-wrap">
            <input
                id={id}
                type={visible ? 'text' : 'password'}
                name={name}
                value={value}
                onChange={onChange}
                placeholder={placeholder}
                minLength={minLength}
                required={required}
                autoComplete={autoComplete ?? (name === 'password_confirmation' ? 'new-password' : name === 'password' ? 'new-password' : 'current-password')}
            />
            <button
                type="button"
                className="password-toggle-btn"
                onClick={() => setVisible((v) => !v)}
                aria-label={visible ? t('Hide password', 'Itago ang password') : t('Show password', 'Ipakita ang password')}
                title={visible ? t('Hide password', 'Itago ang password') : t('Show password', 'Ipakita ang password')}
            >
                {visible ? (
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                        <line x1="1" y1="1" x2="23" y2="23" />
                        <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24" />
                    </svg>
                ) : (
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                )}
            </button>
        </div>
    );
}
