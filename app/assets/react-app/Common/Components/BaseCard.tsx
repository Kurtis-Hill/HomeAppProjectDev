import * as React from 'react';


export function BaseCard(props: {
    loading: boolean;
    children?: React.ReactNode;
    colour?: string;
    cardClasses?: string;
    id?: number;
    setVariableToUpdate?: (n: number) => void;
    setCardLoading?: (loading: boolean) => void;
}): React.ReactElement {
    const colour      = props.colour ?? 'primary';
    const cardClasses = props.cardClasses ?? 'col-xl-3 col-md-6 mb-4';
    const id          = props.id ?? 0;

    const { setVariableToUpdate, setCardLoading } = props;

    const handleClick = () => {
        setCardLoading?.(true);
        setVariableToUpdate?.(id);
    };

    return (
        <div className={cardClasses}>
            <div
                onClick={handleClick}
                className={`shadow h-100 py-2 card border-left-${colour}`}
                style={{ cursor: 'pointer', transition: 'box-shadow 0.2s ease, transform 0.1s ease' }}
                onMouseEnter={(e) => {
                    (e.currentTarget as HTMLDivElement).style.transform    = 'translateY(-2px)';
                    (e.currentTarget as HTMLDivElement).style.boxShadow    = '0 0.5rem 1.5rem rgba(0,0,0,.15)';
                }}
                onMouseLeave={(e) => {
                    (e.currentTarget as HTMLDivElement).style.transform    = '';
                    (e.currentTarget as HTMLDivElement).style.boxShadow    = '';
                }}
            >
                <div className="card-body">
                    {props.children}
                </div>
            </div>
        </div>
    );
}
