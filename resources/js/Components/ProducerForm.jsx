import React, { useState } from 'react';
import Helmet from 'react-helmet';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/inertia-react';
import LoadingButton from '@/Shared/LoadingButton';
import TextInput from '@/Shared/TextInput';

export default ({ producer }) => {
    const { errors } = usePage();
    const [sending, setSending] = useState(false);
    const [values, setValues] = useState({
        first_name: producer?.first_name || '',
        last_name: producer?.last_name || '',
        email: producer?.email || '',
        phone_number: producer?.phone_number || '',
    });

    const handleChange = (e) => {
        const key = e.target.name;
        const value = e.target.value;

        setValues((prevValues) => ({
            ...prevValues,
            [key]: value,
        }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        setSending(true);

        const routeName = producer ? `producers.update` : `producers.store`;
        const routeData = producer ? { id: producer.id, ...values } : values;

        Inertia.post(route(routeName), routeData, {
            onFinish: () => {
                setSending(false);
            },
        });
    };

    return (
        <div className="container mx-auto mt-5">
            <Helmet>
                <title>{producer ? 'Edit Producer' : 'Create Producer'}</title>
            </Helmet>

            <h1 className="text-2xl font-bold mb-5">
                {producer ? 'Edit Producer' : 'Create Producer'}
            </h1>

            <form onSubmit={handleSubmit}>
                <div className="mb-4">
                    <TextInput
                        label="First Name"
                        name="first_name"
                        value={values.first_name}
                        onChange={handleChange}
                        error={errors.first_name}
                    />
                </div>

                <div className="mb-4">
                    <TextInput
                        label="Last Name"
                        name="last_name"
                        value={values.last_name}
                        onChange={handleChange}
                        error={errors.last_name}
                    />
                </div>

                <div className="mb-4">
                    <TextInput
                        label="Email"
                        name="email"
                        type="email"
                        value={values.email}
                        onChange={handleChange}
                        error={errors.email}
                    />
                </div>

                <div className="mb-4">
                    <TextInput
                        label="Phone Number"
                        name="phone_number"
                        value={values.phone_number}
                        onChange={handleChange}
                        error={errors.phone_number}
                    />
                </div>

                <div>
                    <LoadingButton loading={sending}>
                        {producer ? 'Update Producer' : 'Create Producer'}
                    </LoadingButton>
                </div>
            </form>
        </div>
    );
};
