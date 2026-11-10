import * as React from 'react';

export default function DeleteButton(props: {
    clickFunction: (value: any) => void,
}) {
    const { clickFunction } = props;

    return (
        <button onClick={() => clickFunction(true)} type="button" className="btn-modern-danger">
            <i className="fas fa-trash-alt" style={{ fontSize: '0.8rem' }} />
            Delete
        </button>
    )
}
