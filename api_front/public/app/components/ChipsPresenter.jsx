import * as React from 'react';
import { styled } from '@mui/material/styles';
import { Paper, Chip, Typography, Box } from '@mui/material';
import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';

const ListItem = styled('div')(({ theme }) => ({
    margin: theme.spacing(0.5),
}));

export default function ChipsPresenter(props) {
    const { t } = useTranslation('common');
    const { items, handleDelete, title, position } = props;
    const [chips, setChips] = useState([]);

    useEffect(() => {
        setChips(items);
    }, [items])

    function deleteChip(chipToDelete) {
        handleDelete(chipToDelete);
    };

    function makePresentation(chips) {
        return (
            <Paper
                elevation={2}
                sx={{ p: 0.5, m: 0, }}
                component="div"
            >
                <Typography variant='label' >
                    {(chips || []).length > 0 ? t(title) : ""}
                </Typography>
                <Box component="div" sx={{
                    display: 'flex',
                    justifyContent: 'center',
                    flexWrap: 'wrap',
                    listStyle: 'none',
                    border: "1.5px solid #c0c0c0",
                    borderRadius: "5px"
                }} >
                    {chips.map((data) => {

                        return (
                            <ListItem key={data.id}>
                                <Chip
                                    label={data.label}
                                    onDelete={() => deleteChip(data)}
                                />
                            </ListItem>
                        );
                    })}
                </Box>
            </Paper>
        );
    }

    return (
        <>
            {
                chips.length > 0 ?
                    <>
                        <Box component="div">
                            {position === "left" ? makePresentation(chips) : <div />}
                        </Box>
                        <Box component="div">
                            {position === "right" ? makePresentation(chips) : <div />}
                        </Box>
                    </> : <div />
            }
        </>
    );
}
