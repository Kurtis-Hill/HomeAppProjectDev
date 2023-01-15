import * as React from 'react';
// import { useState, useEffect } from 'react';

export default function BaseModal(props: {
    title: string; 
    modalShow: boolean; 
    setShowModal: (show: boolean) => void; 
    modalOpacity?: number|undefined; 
    keyValue?: number|undefined;
    label?: string|undefined; 
    children?: React.ReactNode;
    indexPosition?: number;
    height?: number;
    heightClasses?: string;
}) {
    const keyValue: number = props.keyValue ?? 0;
    const title: string = props.title ?? '';
    const modalOpacity: number = props.modalOpacity ?? 100;
    const modalShow: boolean = props.modalShow ?? true;
    const setShowModal = props.setShowModal;
    const label: string|null = props.label ?? 'Modal';
    const indexPosition: number = props.indexPosition ?? 1050;
    const height: number|string = props.height ?? null;
    const heightClasses: string|null = props.heightClasses ?? null
    
    const toggleModalOff = (): void => {
        setShowModal(false);
    }

    const buildHeightStyle = (): object => {
        if (height !== null) {
            return {
                height: `${height}px`,
            }
        }

        return {}
    } 

    if (modalShow === true) {
        return (
            <React.Fragment>
                <div key={keyValue} style={{ paddingRight: '17px', display: 'block', opacity:`${modalOpacity}%`, zIndex:`${indexPosition}`}} className="modal-show modal" role="dialog" aria-labelledby={label} aria-hidden="true">
                    <div className="modal-dialog" role="document">
                        <div className={`modal-content ${heightClasses}`} style={buildHeightStyle()}>
                            <div className="modal-header">
                                <h5 className="modal-title title">{title}</h5>
                                <button onClick={toggleModalOff} className="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div className={`modal-body ${heightClasses}`}>
                                { props.children }
                            </div>
                        </div>
                    </div>
                </div>
            </React.Fragment>
        );
    }
}
