import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { useLanguage } from '../context/LanguageContext';
import LanguageSwitch from '../components/LanguageSwitch';
import PasswordInput from '../components/PasswordInput';

export default function LoginPage() {
    const { login } = useAuth();
    const { t } = useLanguage();
    const navigate = useNavigate();
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setSuccess('');
        try {
            const data = await login(email, password);
            if (data.success) {
                setSuccess(data.message || t('Login successful!', 'Matagumpay ang pag-log in!'));
                setTimeout(() => navigate('/home'), 600);
            } else {
                setError(data.message || t('Login failed.', 'Hindi matagumpay ang pag-log in.'));
            }
        } catch {
            setError(t('Network error. Please try again.', 'May error sa network. Subukan muli.'));
        }
    };

    return (
        <div className="phone-frame">
            <div className="screen active" id="login">
                <div className="auth-header">
                    <div className="auth-logo">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" /><path d="M7 12h10" /></svg>
                    </div>
                    <h1>ORYZATIX</h1>
                </div>
                <div style={{ position: 'absolute', top: 14, right: 14 }}><LanguageSwitch /></div>
                <div className="auth-card fade-in">
                    <h2>{t('Welcome Back', 'Maligayang Pagbabalik')}</h2>
                    <p className="auth-sub">{t('Log in to continue detecting rice diseases', 'Mag-log in para magpatuloy sa pagtukoy ng mga sakit sa palay')}</p>
                    {error && <div className="error-banner show">{error}</div>}
                    {success && <div className="success-banner show">{success}</div>}
                    <form className="auth-form" onSubmit={handleSubmit}>
                        <div className="form-group">
                            <label>{t('Email Address', 'Email Address')}</label>
                            <input type="email" value={email} onChange={(e) => setEmail(e.target.value)} placeholder="farmer@example.com" required />
                        </div>
                        <div className="form-group">
                            <label>{t('Password', 'Password')}</label>
                            <PasswordInput
                                value={password}
                                onChange={(e) => setPassword(e.target.value)}
                                autoComplete="current-password"
                                required
                            />
                        </div>
                        <button type="submit" className="auth-btn">{t('Sign In', 'Mag-Log In')}</button>
                    </form>
                    <div className="auth-switch">
                        <span>{t("Don't have an account? ", 'Wala ka pang account? ')}</span>
                        <Link to="/register">{t('Create one', 'Gumawa ng isa')}</Link>
                    </div>
                </div>
            </div>
        </div>
    );
}
