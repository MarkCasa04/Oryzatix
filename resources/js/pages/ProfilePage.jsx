import { useState } from 'react';
import { useAuth } from '../context/AuthContext';
import { useLanguage } from '../context/LanguageContext';
import AppLayout from '../components/AppLayout';
import BottomNav from '../components/BottomNav';
import { roleLabel } from '../utils/helpers';

export default function ProfilePage() {
    const { user, logout, updateProfile } = useAuth();
    const { t, lang } = useLanguage();
    const [editing, setEditing] = useState(false);
    const [form, setForm] = useState({ name: user?.name || '', location: user?.location || '' });
    const [message, setMessage] = useState('');

    const initials = (user?.name || 'U').split(' ').map((n) => n[0]).join('').slice(0, 2).toUpperCase();

    const handleSave = async () => {
        try {
            const data = await updateProfile(form);
            if (data.success) {
                setMessage(t('Profile updated!', 'Na-update ang profile!'));
                setEditing(false);
            }
        } catch {
            setMessage(t('Update failed.', 'Hindi na-update.'));
        }
    };

    return (
        <AppLayout>
            <div className="screen active" id="profile">
                <div className="profile-header">
                    <div className="profile-avatar">{initials}</div>
                    <h2>{user?.name || 'User'}</h2>
                    <p>{roleLabel(user?.role, lang)}{user?.location ? ` · ${user.location}` : ''}</p>
                </div>

                {editing ? (
                    <div className="auth-card" style={{ margin: '0 20px 20px' }}>
                        <div className="form-group">
                            <label>{t('Full Name', 'Buong Pangalan')}</label>
                            <input value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} />
                        </div>
                        <div className="form-group">
                            <label>{t('Location', 'Lokasyon')}</label>
                            <input value={form.location} onChange={(e) => setForm({ ...form, location: e.target.value })} />
                        </div>
                        <button type="button" className="auth-btn" onClick={handleSave}>{t('Save', 'I-save')}</button>
                        <button type="button" className="auth-btn" style={{ marginTop: 8, background: 'var(--neutral-400)' }} onClick={() => setEditing(false)}>{t('Cancel', 'Kanselahin')}</button>
                    </div>
                ) : (
                    <div className="profile-menu">
                        <div className="profile-menu-item" onClick={() => { setForm({ name: user?.name || '', location: user?.location || '' }); setEditing(true); }} role="button" tabIndex={0}>
                            <div className="pm-icon green-bg"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" /></svg></div>
                            <span className="pm-text">{t('Edit Profile', 'I-edit ang Profile')}</span>
                        </div>
                        <div className="profile-menu-item" onClick={logout} role="button" tabIndex={0} style={{ marginTop: 16 }}>
                            <div className="pm-icon red-bg"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" /><polyline points="16 17 21 12 16 7" /></svg></div>
                            <span className="pm-text" style={{ color: 'var(--red-500)' }}>{t('Log Out', 'Mag-Log Out')}</span>
                        </div>
                    </div>
                )}
                {message && <p style={{ textAlign: 'center', color: 'var(--green-600)', fontSize: 13, padding: '0 20px' }}>{message}</p>}
                <div className="app-version">
                    ORYZATIX v1.0<br />
                    <span>{t('John Paul College · BSIT Department', 'John Paul College · Kagawaran ng BSIT')}</span>
                </div>
                <BottomNav active="profile" />
            </div>
        </AppLayout>
    );
}
