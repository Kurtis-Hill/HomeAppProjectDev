import * as React from 'react';
import { useEffect } from 'react';

export default function BaseModal(props: {
    title: string;
    modalShow: boolean;
    setShowModal: (show: boolean) => void;
    modalOpacity?: number;
    keyValue?: number;
    label?: string;
    children?: React.ReactNode;
    indexPosition?: number;
    /** @deprecated height is now managed automatically */
    height?: number;
    /** @deprecated use max-width via maxWidth instead */
    heightClasses?: string;
    maxWidth?: string;
}) {
    const {
        title,
        modalShow,
        setShowModal,
        modalOpacity = 100,
        keyValue = 0,
        indexPosition = 1050,
        maxWidth,
        label = 'Modal',
    } = props;

    // Close on Escape key
    useEffect(() => {
        if (!modalShow) return;
        const onKey = (e: KeyboardEvent) => {
            if (e.key === 'Escape') setShowModal(false);
        };
        document.addEventListener('keydown', onKey);
        return () => document.removeEventListener('keydown', onKey);
    }, [modalShow, setShowModal]);

    if (!modalShow) return null;

    const handleBackdropClick = (e: React.MouseEvent<HTMLDivElement>) => {
        if (e.target === e.currentTarget) setShowModal(false);
    };

    return (
        <div
            key={keyValue}
            className="modal-overlay"
            style={{ zIndex: indexPosition, opacity: modalOpacity / 100 }}
            onClick={handleBackdropClick}
            role="dialog"
            aria-modal="true"
            aria-label={label}
        >
            <div
                className="modal-modern"
                style={maxWidth ? { maxWidth } : undefined}
            >
                <div className="modal-modern-header">
                    <h5 className="modal-modern-title">{title}</h5>
                    <button
                        className="modal-modern-close"
                        onClick={() => setShowModal(false)}
                        type="button"
                        aria-label="Close"
                    >
                        ✕
                    </button>
                </div>
                <div className="modal-modern-body">
                    {props.children}
                </div>
            </div>
        </div>
    );
}
