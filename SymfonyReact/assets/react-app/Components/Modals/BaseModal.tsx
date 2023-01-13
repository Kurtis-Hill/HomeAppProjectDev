import * as React from 'react';
// import { useState, useEffect } from 'react';

export default function BaseModal(props: {
    title: string; 
    content: string|React|null; 
    modalShow: boolean; 
    setShowModal: (show: boolean) => void; 
    modalOpacity?: number|undefined; 
    keyValue?: number|undefined;
    label?: string|undefined; 
}) {
    const keyValue: number = props.keyValue ?? 0;
    const title: string = props.title ?? '';
    const content: string|null = props.content ?? null;
    const modalOpacity: number = props.modalOpacity ?? 100;
    const modalShow: boolean = props.modalShow ?? true;
    const setShowModal = props.setShowModal;
    const label: string|null = props.label ?? 'Modal';
    
    const toggleModalOff = (): void => {
        setShowModal(false);
    }

    if (modalShow === true) {
        return (
            <React.Fragment>
                <div key={keyValue} style={{ paddingRight: '17px', display: 'block', opacity:`${modalOpacity}%` }} className="modal-show modal" tabIndex={-1} role="dialog" aria-labelledby={label} aria-hidden="true">
                    <div className="modal-dialog" role="document">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title title">{title}</h5>
                                <button onClick={toggleModalOff} className="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div className="modal-body">
                                { content }
                            </div>
                        </div>
                    </div>
                </div>
            </React.Fragment>
        );
    }
}
