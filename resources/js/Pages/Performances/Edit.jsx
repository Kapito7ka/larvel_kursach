import React, { useState } from 'react';
import Helmet from 'react-helmet';
import { Inertia } from '@inertiajs/inertia';
import { Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.jsx';
import DeleteButton from '@/Components/DeleteButton';
import LoadingButton from '@/Components/LoadingButton';
import TextInput from '@/Components/TextInput';
import TrashedMessage from '@/Components/TrashedMessage';

export default function EditPerformance() {
    const { performance, errors } = usePage().props;
    const [sending, setSending] = useState(false);

    const [values, setValues] = useState({
        title: performance.title || '',
        duration: performance.duration || '',
    });

    function handleChange(e) {
        const key = e.target.name;
        const value = e.target.value;
        setValues((values) => ({
            ...values,
            [key]: value,
        }));
    }

    function handleSubmit(e) {
        e.preventDefault();
        setSending(true);
        Inertia.put(route('performances.update', performance.id), values).then(() =>
            setSending(false)
        );
    }

    function destroy() {
        if (confirm('Are you sure you want to delete this performance?')) {
            Inertia.delete(route('performances.destroy', performance.id));
        }
    }

    return (
        <AuthenticatedLayout>
            <div>
                <Helmet title={values.title} />
                <h1 className="mb-8 font-bold text-3xl">
                    <Link
                        href={route('performances')}
                        className="text-indigo-600 hover:text-indigo-700"
                    >
                        Performances
                    </Link>
                    <span className="text-indigo-600 font-medium mx-2">/</span>
                    {values.title}
                </h1>
                {performance.deleted_at && (
                    <TrashedMessage>
                        This performance has been deleted.
                    </TrashedMessage>
                )}
                <div className="bg-white rounded shadow overflow-hidden max-w-3xl">
                    <form onSubmit={handleSubmit}>
                        <div className="p-8 -mr-6 -mb-8 flex flex-wrap">
                            <TextInput
                                className="pr-6 pb-8 w-full lg:w-1/2"
                                label="Title"
                                name="title"
                                errors={errors.title}
                                value={values.title}
                                onChange={handleChange}
                            />
                            <TextInput
                                className="pr-6 pb-8 w-full lg:w-1/2"
                                label="Duration (minutes)"
                                name="duration"
                                type="number"
                                errors={errors.duration}
                                value={values.duration}
                                onChange={handleChange}
                            />
                        </div>
                        <div className="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
                            {!performance.deleted_at && (
                                <DeleteButton onDelete={destroy}>
                                    Delete Performance
                                </DeleteButton>
                            )}
                            <LoadingButton
                                loading={sending}
                                type="submit"
                                className="btn-indigo ml-auto"
                            >
                                Update Performance
                            </LoadingButton>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
};
