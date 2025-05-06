import React, { useEffect, useState } from 'react';
import { Paper, Box } from '@mui/material';
import DOMPurify from 'dompurify';
import ReactQuill from 'react-quill';

const BlocComment = (props) => {
    const { isOpen, onClose } = props
    const [text, setText] = useState('');

    useEffect(() => {
        if (!isOpen) {
            commit(text);
        }
    }, [isOpen])

    const commit = (text) => {
        const purifiedText = DOMPurify.sanitize(text);
        onClose(purifiedText);
    };

    const handleEditorChange = (value) => {
        setText(value);
    };


    return (
        <>
            {isOpen && (
                <Box
                    sx={{
                        position: 'absolute',
                        zIndex: 1000,
                        height: "100%",
                    }}
                >
                    <Paper sx={{ width: '32em', height: '26em' }}>
                        <ReactQuill
                            theme='snow'
                            value={text}
                            onChange={handleEditorChange}
                            modules={{
                                toolbar: [
                                    ['bold', 'italic', 'underline', 'strike'],
                                    ['blockquote', 'code-block'],
                                    // [{ header: 1 }, { header: 2 }],
                                    // [{ list: 'ordered' }, { list: 'bullet' }],
                                    // [{ script: 'sub' }, { script: 'super' }],
                                    [{ indent: '-1' }, { indent: '+1' }],
                                    [{ direction: 'rtl' }],
                                    // [{ size: ['small', false, 'large', 'huge'] }],
                                    // [{ header: [1, 2, 3, 4, 5, 6, false] }],
                                    // [{ color: [] }, { background: [] }],
                                    // [{ font: [] }],
                                    [{ align: [] }],
                                    // ['clean'],
                                ],
                            }}
                            // formats={['bold', 'italic', 'underline', 'strike', 'blockquote', 'code-block', 'header', 'list', 'script', 'indent', 'direction', 'size', 'color', 'font', 'align',]}
                            style={{ height: '26em' }}
                            scrollingContainer=".ql-editor"
                        />
                    </Paper>
                </Box>
            )}
        </>
    );
};

export default BlocComment;
