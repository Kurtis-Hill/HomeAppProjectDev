import * as React from 'react';
import { useState } from 'react';
import BaseModal from '../../../../Common/Components/Modals/BaseModal';

import { CardDisplayForm } from "../Form/CardDisplayForm";
import { CardFormResponseInterface } from '../Response/FormResponse/CardFormResponseInterface';

export function CardDisplayModal(props: {children?: React.ReactNode; cardViewID: number|null;}) {
    // const [showModal, setShowModal] = useState<boolean>(false);
    // const [loading, setLoading] = useState<boolean>(false);
    // const [cardFormData, setCardFormData] = useState<CardFormResponseInterface>();
    // const [cardViewID, setCardViewID] = useState<number|null>(null);
    const cardViewID = props.cardViewID;

console.log('cardVieID', cardViewID)
    return (
        <>
            <BaseModal
                title={'updateID'}
                modalShow={false}
                setShowModal={() => {}}
            >
            
                { props.children }
            </BaseModal>
        </>
    )
}