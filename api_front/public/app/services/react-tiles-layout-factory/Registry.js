import React from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
//MUI
import { Card, CardActions, CardContent, Typography, CardMedia, Button } from '@mui/material';

//generic card
export class MyCard extends React.Component {
    //add MUI compo
    static propTypes = {
        item: PropTypes.object
    };

    render() {
        var button = "";
        if (this.props.item.path === "/admin") {
            //go to orchid admin panel
            button =
                <Button
                    href={this.props.item.path}
                    variant="contained"
                    sx={{ mt: 3, ml: 1 }}
                    color="primary"
                >{this.props.item.linkLabel}</Button>;
        } else {
            //for react router
            var slug = this.props.item.slug ? "/" + this.props.item.slug : "";
            button = <Button
                component={Link}
                to={this.props.item.path + slug}
                variant="contained"
                sx={{ mt: 3, ml: 1 }}
                color="primary"
            >{this.props.item.linkLabel}</Button>;
        }

        return (<Card sx={{ minWidth: '100%', minHeight: '100%' }} className="myCard">
            <CardContent>
                <Typography variant="h5" component="div">
                    {this.props.item.title}
                </Typography>
                <Typography variant="body2">
                    {this.props.item.description}
                </Typography>
            </CardContent>
            <CardActions>
                {button}
            </CardActions>
        </Card>);
    }
}

//description on page
export class Hello extends React.Component {
    //add MUI compo
    static propTypes = {
        item: PropTypes.object
    };
    render() {
        return (
            <Card sx={{ minWidth: '100%', minHeight: '100%' }} className="myCard">
                <CardContent>
                    <Typography variant="h5" component="div">
                        {this.props.item.title}
                    </Typography>
                    <Typography variant="body2">
                        {this.props.item.description}
                    </Typography>
                </CardContent>
            </Card>
        );
    }
}
