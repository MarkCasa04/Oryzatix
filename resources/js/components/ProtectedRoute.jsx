import { Navigate, useLocation } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

export default function ProtectedRoute({ children }) {
    const { isAuthenticated, loading } = useAuth();
    const location = useLocation();

    if (loading) {
        return (
            <div className="phone-frame">
                <div className="screen active" id="loading" style={{ display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                    <div className="loading-ring" />
                </div>
            </div>
        );
    }

    if (!isAuthenticated) {
        return <Navigate to="/login" state={{ from: location }} replace />;
    }

    return children;
}

export function GuestRoute({ children }) {
    const { isAuthenticated, loading } = useAuth();

    if (loading) {
        return (
            <div className="phone-frame">
                <div className="screen active" style={{ display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                    <div className="loading-ring" />
                </div>
            </div>
        );
    }
    if (isAuthenticated) return <Navigate to="/home" replace />;
    return children;
}
