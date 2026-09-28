import { DesktopSidebar } from './BottomNav';

export default function AppLayout({ children }) {
    return (
        <div className="app-shell">
            <DesktopSidebar />
            <div className="phone-frame">{children}</div>
        </div>
    );
}
