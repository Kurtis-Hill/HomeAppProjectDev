import * as React from 'react';

export default function DeleteButton(props: {
    clickFunction: (value: any) => void,
}) {
    const { clickFunction } = props;

    return (
        <button onClick={() => clickFunction(true)} type="button" className="btn btn-danger btn-lg btn-block">Delete</button>
    )
}
