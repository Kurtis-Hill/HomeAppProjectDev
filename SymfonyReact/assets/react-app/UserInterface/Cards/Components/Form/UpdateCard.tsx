import * as React from 'react';
import { useState, useEffect, useRef } from 'react';
import CardViewResponseInterface from '../../Response/CardView/CardViewResponseInterface';
import { AnnouncementFlashModalBuilder } from '../../../../Common/Builders/ModalBuilder/AnnouncementFlashModalBuilder';
import SubmitButton from '../../../../Common/Components/Buttons/SubmitButton';
import { AnnouncementFlashModal } from '../../../../Common/Components/Modals/AnnouncementFlashModal';
import { getCardViewFormRequest } from '../../Request/Card/GetCardViewFormRequest';
import { StandardCardFormResponseInterface } from '../../Response/CardForms/StandardCardFormResponseInterface';
import DotCircleSpinner from '../../../../Common/Components/Spinners/DotCircleSpinner';
import { capitalizeFirstLetter } from '../../../../Common/StringFormatter';
import { IconResponseInterface } from '../../../Response/Icons/IconResponseInterface';
import { FormInlineSelectWLabel } from '../../../../Common/Components/Selects/FormInlineSelectWLabel';
import { Label } from '../../../../Common/Components/Elements/Label';
import { ColourResponseInterface } from '../../../Response/Colour/ColourResponseInterface';


export function UpdateCard(props: {cardViewID: number}) {
    const { cardViewID } = props;

    const [announcementModals, setAnnouncementModals] = useState<Array<typeof AnnouncementFlashModal>>([]);

    const [updateCardRequestLoading, setUpdateCardRequestLoading] = useState<boolean>(false);
    
    const updateCardRequestSuccess = useRef<boolean>(false);

    const [cardViewUserForm, setCardViewUserForm] = useState<StandardCardFormResponseInterface|null>(null);

    const showAnnouncementFlash = (message: Array<string>, title: string, timer?: number | null): void => {
        setAnnouncementModals([
            <AnnouncementFlashModalBuilder
                setAnnouncementModals={setAnnouncementModals}
                title={title}
                dataToList={message}
                timer={timer ? timer : 40}
            />
        ])
    }
    
    const handleUpdateCardRequest = (): void => {
        setUpdateCardRequestLoading(true);
    }

    const handleCardViewFormRequest = async () => {
        const cardViewUserFormResponse = await getCardViewFormRequest(cardViewID);

        const cardViewUserFormResponseData: StandardCardFormResponseInterface = cardViewUserFormResponse.data.payload;

        if (cardViewUserFormResponse.status === 200) {
            setCardViewUserForm(cardViewUserFormResponseData);
            // setCardViewInitialLoad(false);

            console.log('cardViewUserFormResponseData', cardViewUserFormResponseData);
        }
    }

    useEffect(() => {
        console.log('useEffect Ran');
        handleCardViewFormRequest();
    }, [cardViewID])

    const updateCardFormInput = (e: Event): void => {
        const target = e.target as HTMLInputElement;
        const name = target.name;
        const value = target.value;

        console.log('name', name);
        console.log('value', value);
        // console.log('e', e);
        
        switch (name) {
            case 'card-icon':
                for (let i = 0; i < cardViewUserForm.cardUserSelectionOptions.icons.length; i++) {
                    if (cardViewUserForm.cardUserSelectionOptions.icons[i].iconID === parseInt(value)) {
                        console.log('cardViewUserForm.cardUserSelectionOptions.icons[i].iconName', cardViewUserForm.cardUserSelectionOptions.icons[i].iconName);
                        setCardViewUserForm((prevState: StandardCardFormResponseInterface) => ({
                            ...prevState,
                            currentCardIcon: {
                                description: cardViewUserForm.cardUserSelectionOptions.icons[i].description,
                                iconName: cardViewUserForm.cardUserSelectionOptions.icons[i].iconName,
                                iconID: cardViewUserForm.cardUserSelectionOptions.icons[i].iconID,
                            }
                        })) 
                    }
                }
                break;
            case 'card-colour':
                for (let i = 0; i < cardViewUserForm.cardUserSelectionOptions.colours.length; i++) {
                    if (cardViewUserForm.cardUserSelectionOptions.colours[i].colourID === parseInt(value)) {
                        console.log('cardViewUserForm.cardUserSelectionOptions.colours[i].colour', cardViewUserForm.cardUserSelectionOptions.colours[i].colour);
                        setCardViewUserForm((prevState: StandardCardFormResponseInterface) => ({
                            ...prevState,
                            currentCardColour: {
                                shade: cardViewUserForm.cardUserSelectionOptions.colours[i].shade,
                                colour: cardViewUserForm.cardUserSelectionOptions.colours[i].colour,
                                colourID: cardViewUserForm.cardUserSelectionOptions.colours[i].colourID,
                            }
                        }))
                    }
                }
                break;
                default:
                    setCardViewUserForm({
                        ...cardViewUserForm,
                        [name]: value
                })
                break;
        } 
        console.log('cardViewUserForm', cardViewUserForm);       

        // console.log('lol', cardViewUserForm);
    }

    if (cardViewUserForm === null) {
        return (
            <DotCircleSpinner spinnerSize={3} classes="center-absolute-center" />
        )
    }
    
    return (
        <>
            {
                announcementModals.map((announcementModal: typeof AnnouncementFlashModal, index: number) => {
                    return (
                        <React.Fragment key={index}>
                            { announcementModal }
                        </React.Fragment>
                    );
                })
            }
            <form>
                <Label 
                    text="Icon Selection"
                    htmlFor='card-icon'
                />
                <br />
                <select name="card-icon" id="icon-select" value={cardViewUserForm.currentCardIcon.iconID} onChange={(e: Event, ) => {updateCardFormInput(e)}} className="form-space">
                    {cardViewUserForm.cardUserSelectionOptions.icons.map((icon: IconResponseInterface) => (
                        <option key={icon.iconID} value={icon.iconID}>{capitalizeFirstLetter(icon.iconName)}</option>
                    ))}
                </select>
                <i className={`fas fa-2x text-gray-300 modal-icon fa-${cardViewUserForm.currentCardIcon.iconName}`}></i>
                <br />

                <Label 
                    text="Colour Selection"
                    htmlFor='card-colour'
                />
                <select name="card-colour" value={cardViewUserForm.currentCardColour.colourID} onChange={(e: Event) => {updateCardFormInput(e)}} className="form-control">
                    {cardViewUserForm.cardUserSelectionOptions.colours.map((colour: ColourResponseInterface) => (
                    <option value={colour.colourID} key={colour.colourID}>{capitalizeFirstLetter(colour.colour)}</option>
                    ))}
                </select>

                <SubmitButton
                    type="submit"
                    text='Update Card'
                    name='update-card'
                    action='submit'
                    classes='update-card'
                    onClickFunction={() => void(0)}
                />
            </form>

        </>
    )
}