import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider } from './context/AuthContext';
import { LanguageProvider } from './context/LanguageContext';
import { ScanProvider } from './context/ScanContext';
import ProtectedRoute, { GuestRoute } from './components/ProtectedRoute';
import LoginPage from './pages/LoginPage';
import RegisterPage from './pages/RegisterPage';
import HomePage from './pages/HomePage';
import ScanPage from './pages/ScanPage';
import LoadingPage from './pages/LoadingPage';
import ResultsPage from './pages/ResultsPage';
import TreatmentPage from './pages/TreatmentPage';
import HistoryPage from './pages/HistoryPage';
import ConsultationPage from './pages/ConsultationPage';
import ProfilePage from './pages/ProfilePage';

export default function App() {
    return (
        <LanguageProvider>
            <AuthProvider>
                <ScanProvider>
                    <BrowserRouter>
                        <Routes>
                            <Route path="/" element={<Navigate to="/login" replace />} />
                            <Route path="/login" element={<GuestRoute><LoginPage /></GuestRoute>} />
                            <Route path="/register" element={<GuestRoute><RegisterPage /></GuestRoute>} />
                            <Route path="/home" element={<ProtectedRoute><HomePage /></ProtectedRoute>} />
                            <Route path="/scan" element={<ProtectedRoute><ScanPage /></ProtectedRoute>} />
                            <Route path="/loading" element={<ProtectedRoute><LoadingPage /></ProtectedRoute>} />
                            <Route path="/results" element={<ProtectedRoute><ResultsPage /></ProtectedRoute>} />
                            <Route path="/treatment" element={<ProtectedRoute><TreatmentPage /></ProtectedRoute>} />
                            <Route path="/history" element={<ProtectedRoute><HistoryPage /></ProtectedRoute>} />
                            <Route path="/consultation" element={<ProtectedRoute><ConsultationPage /></ProtectedRoute>} />
                            <Route path="/profile" element={<ProtectedRoute><ProfilePage /></ProtectedRoute>} />
                            <Route path="/rice-detector" element={<Navigate to="/login" replace />} />
                            <Route path="*" element={<Navigate to="/login" replace />} />
                        </Routes>
                    </BrowserRouter>
                </ScanProvider>
            </AuthProvider>
        </LanguageProvider>
    );
}
