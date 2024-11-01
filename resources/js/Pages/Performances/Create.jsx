import React from 'react';
import { useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.jsx';
import TextInput from '@/Components/TextInput';
import LoadingButton from '@/Components/LoadingButton';

export default function Create() {
    const { data, setData, post, errors, processing } = useForm({
        title: '',
        duration: '',
    });

    const handleChange = (e) => {
        const key = e.target.name;
        const value = e.target.value;
        setData(key, value);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('performances.store'));
    };

    return (
        <AuthenticatedLayout>
            <div className="max-w-3xl mx-auto bg-white rounded shadow overflow-hidden">
                <form onSubmit={handleSubmit}>
                    <div className="p-8 -mr-6 -mb-8 flex flex-wrap">
                        <TextInput
                            className="pr-6 pb-8 w-full"
                            label="Title"
                            name="title"
                            errors={errors.title}
                            value={data.title}
                            onChange={handleChange}
                        />
                        <TextInput
                            className="pr-6 pb-8 w-full"
                            label="Duration (minutes)"
                            name="duration"
                            type="number"
                            errors={errors.duration}
                            value={data.duration}
                            onChange={handleChange}
                        />
                    </div>
                    <div className="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
                        <LoadingButton
                            loading={processing}
                            type="submit"
                            className="btn-indigo ml-auto"
                        >
                            Create Performance
                        </LoadingButton>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
