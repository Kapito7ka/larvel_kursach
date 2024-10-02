import React, { useState } from 'react';
import Helmet from 'react-helmet';
import { Inertia } from '@inertiajs/inertia';
import {Link, usePage} from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.jsx';
import DeleteButton from '@/Components/DeleteButton';
import LoadingButton from '@/Components/LoadingButton';
import TextInput from '@/Components/TextInput';
import SelectInput from '@/Components/SelectInput';
import TrashedMessage from '@/Components/TrashedMessage';

export default () => {
    const { actor, organizations, errors } = usePage().props;
    console.log(actor);
    const [sending, setSending] = useState(false);

    const [values, setValues] = useState({
        first_name: actor.first_name || '',
        last_name: actor.last_name || '',
        organization_id: actor.organization_id || '',
        email: actor.email || '',
        phone: actor.phone || '',
        address: actor.address || '',
        city: actor.city || '',
        region: actor.region || '',
        country: actor.country || '',
        postal_code: actor.postal_code || ''
    });

    function handleChange(e) {
        const key = e.target.name;
        const value = e.target.value;
        setValues(values => ({
            ...values,
            [key]: value
        }));
    }

    function handleSubmit(e) {
        e.preventDefault();
        setSending(true);
        Inertia.put(route('actors.update', actor.id), values).then(() =>
            setSending(false)
        );
    }

    function destroy() {
        if (confirm('Are you sure you want to delete this actor?')) {
            Inertia.delete(route('actors.destroy', actor.id));
        }
    }

    function restore() {
        if (confirm('Are you sure you want to restore this actor?')) {
            Inertia.put(route('actors.restore', actor.id));
        }
    }

    return (
        <AuthenticatedLayout>
            <div>
                <Helmet title={`${values.first_name} ${values.last_name}`} />
                <h1 className="mb-8 font-bold text-3xl">
                    <Link
                        href={route('actors')}
                        className="text-indigo-600 hover:text-indigo-700"
                    >
                        Actors
                    </Link>
                    <span className="text-indigo-600 font-medium mx-2">/</span>
                    {values.first_name} {values.last_name}
                </h1>
                {actor.deleted_at && (
                    <TrashedMessage onRestore={restore}>
                        This contact has been deleted.
                    </TrashedMessage>
                )}
                <div className="bg-white rounded shadow overflow-hidden max-w-3xl">
                    <form onSubmit={handleSubmit}>
                        <div className="p-8 -mr-6 -mb-8 flex flex-wrap">
                            <TextInput
                                className="pr-6 pb-8 w-full lg:w-1/2"
                                label="First Name"
                                name="first_name"
                                errors={errors.first_name}
                                value={values.first_name}
                                onChange={handleChange}
                            />
                            <TextInput
                                className="pr-6 pb-8 w-full lg:w-1/2"
                                label="Last Name"
                                name="last_name"
                                errors={errors.last_name}
                                value={values.last_name}
                                onChange={handleChange}
                            />
                            <TextInput
                                className="pr-6 pb-8 w-full lg:w-1/2"
                                label="Phone"
                                name="phone_number"
                                type="text"
                                errors={errors.phone_number}
                                value={values.phone_number}
                                onChange={handleChange}
                            />

                        </div>
                        <div className="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
                            {!actor.deleted_at && (
                                <DeleteButton onDelete={destroy}>Delete Contact</DeleteButton>
                            )}
                            <LoadingButton
                                loading={sending}
                                type="submit"
                                className="btn-indigo ml-auto"
                            >
                                Update Contact
                            </LoadingButton>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
};
