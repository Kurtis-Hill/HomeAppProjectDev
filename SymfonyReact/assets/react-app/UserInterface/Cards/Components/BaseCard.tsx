import * as React from 'react';
import { useState } from 'react';

import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';
import CardReadingFactory from '../Factories/CardReadingFactory';

export function BaseCard(props: { 
    loading: boolean;
    children?: React.ReactNode; 
    colour?: string; 
    cardClasses?: string; 
    id?: number; 
    setVariableToUpdate?: (number: number) => void;
    setCardLoading?: (loading: boolean) => void;
 }): React {
    const colour: string = props.colour ?? 'primary';
    const cardClasses = props.cardClasses ?? 'col-xl-3 col-md-6 mb-4 hover'
    const id = props.id ?? 0;
    const loading = props.loading ?? false;

    const { setVariableToUpdate, setCardLoading } = props;

    const handleGeneralCardClick = () => {
        setCardLoading(true);
        setVariableToUpdate(id);
    }

    return (
        <>
            <div className={`${cardClasses}`} key={1}>
                <div onClick={() => handleGeneralCardClick()} className={`shadow h-100 py-2 card border-left-${colour}`}>
                    <div className="card-body">
                        { props.children }
                    </div>
                </div>
            </div>
        </>
    );
}