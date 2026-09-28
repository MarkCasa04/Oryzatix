import { useEffect, useState } from 'react';
import { useLanguage } from '../context/LanguageContext';
import AppLayout from '../components/AppLayout';

const STEPS = ['step1', 'step2', 'step3', 'step4'];

export default function LoadingPage() {
    const { t } = useLanguage();
    const [doneSteps, setDoneSteps] = useState([]);

    useEffect(() => {
        STEPS.forEach((step, i) => {
            setTimeout(() => setDoneSteps((prev) => [...prev, step]), 800 * (i + 1));
        });
    }, []);

    const labels = [
        t('Image pre-processing', 'Image pre-processing'),
        t('Feature extraction', 'Feature extraction'),
        t('MobileNet classification', 'MobileNet classification'),
        t('Severity assessment', 'Pagtasa ng Severity'),
    ];

    return (
        <AppLayout>
            <div className="screen active" id="loading">
                <div className="loading-ring" />
                <h2>{t('Analyzing Image', 'Sinusuri ang Larawan')}</h2>
                <p>{t('Our AI model is scanning your rice leaf image for disease indicators.', 'Sinisuri ng aming AI model ang larawan ng dahon ng palay para sa mga senyales ng sakit.')}</p>
                <div className="loading-steps">
                    {STEPS.map((step, i) => (
                        <div key={step} className={`loading-step ${doneSteps.includes(step) ? 'done' : ''}`} id={step}>
                            <div className="step-check">
                                {doneSteps.includes(step) && (
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3"><polyline points="20 6 9 17 4 12" /></svg>
                                )}
                            </div>
                            <span>{labels[i]}</span>
                        </div>
                    ))}
                </div>
            </div>
        </AppLayout>
    );
}
