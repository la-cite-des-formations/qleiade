import React from "react";
import { useNavigate } from "react-router-dom";
import { withSanctum } from "react-sanctum";

import LoginWithGoogle from "@components/LoginWithGoogle";
//MUI
import { Avatar, Button, TextField, FormControlLabel, Checkbox, Box, Typography, Container, Divider } from "@mui/material";
import LockOutlinedIcon from "@mui/icons-material/LockOutlined";

const SignIn = ({ signIn, setUser }) => {

    const nav = useNavigate();

    const handleSubmit = (event) => {
        event.preventDefault();
        const data = new FormData(event.currentTarget);

        var remember = data.get("remember") != null;
        var email = data.get("email");
        var password = data.get("password");

        signIn(email, password, remember)
            .then(data => {
                if (data.signedIn) {
                    setUser(data.user, data.authenticated);
                    nav("/home");
                }
            })
            .catch(() => window.alert("Incorrect email or password"));
    };

    return (
        <Container component="main" maxWidth="xs">
            <Box
                sx={{
                    marginTop: 8,
                    display: "flex",
                    flexDirection: "column",
                    alignItems: "center"
                }}
            >
                <Avatar sx={{ m: 1, bgcolor: "secondary.main" }}>
                    <LockOutlinedIcon />
                </Avatar>
                <Typography component="h1" variant="h5">
                    Connectez vous à Qléiade
                </Typography>
                <Divider variant="middle" />
                <Box sx={{
                    mt: 1,
                    display: "flex",
                    flexDirection: "column",
                    alignItems: "center"
                }}>
                    <Typography variant="text.secondary" component="div">
                        Avec votre compte Google
                    </Typography>
                    <LoginWithGoogle />
                </Box>
                <Divider variant="middle" />
                <Box sx={{
                    mt: 1,
                    display: "flex",
                    flexDirection: "column",
                    alignItems: "center"
                }}>
                    <Typography variant="text.secondary" component="div">
                        Avec vos identifiants Qléiade
                    </Typography>
                </Box>

                <Box
                    component="form"
                    onSubmit={handleSubmit}
                    noValidate
                    sx={{ mt: 1 }}
                >

                    <TextField
                        margin="normal"
                        required
                        fullWidth
                        id="email"
                        label="Email Address"
                        name="email"
                        autoComplete="email"
                        autoFocus
                    />
                    <TextField
                        margin="normal"
                        required
                        fullWidth
                        name="password"
                        label="Password"
                        type="password"
                        id="password"
                        autoComplete="current-password"
                    />
                    <FormControlLabel
                        control={<Checkbox name="remember" value="remember" color="primary" />}
                        label="Remember me"
                    />
                    <Button
                        type="submit"
                        fullWidth
                        variant="contained"
                        sx={{ mt: 3, mb: 2 }}
                    >
                        Sign In
                    </Button>
                    {/* <Grid container>
                        <Grid item xs>
                            <Link href="#" variant="body2">
                                Forgot password?
                            </Link>
                        </Grid>
                        <Grid item>
                            <Link href="#" variant="body2">
                                {"Don't have an account? Sign Up"}
                            </Link>
                        </Grid>
                    </Grid> */}
                </Box>
            </Box>

        </Container>
    );
}

export default withSanctum(SignIn);
