import React, { useEffect, useState } from "react";

export default function Filters(props) {
    const { choices } = props;
    const [selectedChoice, setSelectedChoice] = useState(0);

    const handleCheckboxChange = (choice) => {
        if (selectedChoice === choice.id) {
            setSelectedChoice(0);
        } else {
            setSelectedChoice(choice.id);
        }
    };

    useEffect(() => {
        props.commit(selectedChoice)
    }, [selectedChoice])

    return (
        <div>
            {choices.map((choice) => (
                <div key={choice.id}>
                    <label>
                        <input
                            type="checkbox"
                            checked={selectedChoice === choice.id}
                            onChange={() => handleCheckboxChange(choice)}
                        />
                        {choice.order + "." + choice.label}
                    </label>
                </div>
            ))}
        </div>
    );
}
