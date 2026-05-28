import * as React from 'react';
import { useState, useEffect } from 'react';

export interface AnnouncementFlashModalInterface {
    title: string;
    errors: string[];
    errorNumber: number;
    timer: number;
    setAnnouncementModals?: (announcementModals: Array<typeof AnnouncementFlashModal>) => void;
    modalShow?: boolean;
    setErrorCount?: (errorCount: number) => void;
}

export function AnnouncementFlashModal(props: AnnouncementFlashModalInterface) {
    const { title, errors, errorNumber, timer, setAnnouncementModals } = props;

    const [opacity, setOpacity] = useState<number>(1);
    const [visible, setVisible] = useState<boolean>(props.modalShow ?? true);

    useEffect(() => {
        if (!visible) return;

        // Start fading after twice the timer, then disappear
        const fadeDelay = setTimeout(() => {
            setOpacity(0);
        }, Math.max(timer * 2, 1500));

        const hideDelay = setTimeout(() => {
            setVisible(false);
            setAnnouncementModals?.([]);
        }, Math.max(timer * 2 + 350, 1850));

        return () => {
            clearTimeout(fadeDelay);
            clearTimeout(hideDelay);
        };
    }, [visible]);

    if (!visible) return null;

    const variantClass =
        title.toLowerCase().includes('success') ? 'toast-success'
        : title.toLowerCase().includes('error')  ? 'toast-error'
        : title.toLowerCase().includes('warn')   ? 'toast-warning'
        : '';

    return (
        <div className="toast-stack">
            <div
                key={errorNumber}
                className={`toast-card ${variantClass}`}
                style={{ opacity, transition: 'opacity 0.35s ease' }}
                onClick={() => { setVisible(false); setAnnouncementModals?.([]); }}
                title="Click to dismiss"
            >
                <div className="toast-title">{title}</div>
                {errors.length > 0 && (
                    <ul className="toast-messages">
                        {errors.map((msg, i) => (
                            <li key={i}>{msg}</li>
                        ))}
                    </ul>
                )}
            </div>
        </div>
    );
}
