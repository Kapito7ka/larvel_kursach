import React from 'react';
import { useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.jsx';
import TextInput from '@/Components/TextInput';
import SelectInput from '@/Components/SelectInput';
import LoadingButton from '@/Components/LoadingButton';

export default function Create({ performances, halls }) {
    const { data, setData, post, errors, processing } = useForm({
        performance_id: '',
        datetime: '',
        price: '',
        hall_id: '',
    });

    const handleChange = (e) => {
        const key = e.target.name;
        const value = e.target.value;
        setData(key, value);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('shows.store')); // Ensure your route is correct
    };

    return (
        <AuthenticatedLayout>
            <div className="max-w-3xl mx-auto bg-white rounded shadow overflow-hidden">
                <form onSubmit={handleSubmit}>
                    <div className="p-8 -mr-6 -mb-8 flex flex-wrap">
                        <SelectInput
                            className="pr-6 pb-8 w-full lg:w-1/2"
                            label="Performance"
                            name="performance_id"
                            errors={errors.performance_id}
                            value={data.performance_id}
                            onChange={handleChange}
                        >
                            <option value="">Select a performance</option>
                            {performances.map((performance) => (
                                <option key={performance.id} value={performance.id}>
                                    {performance.title}
                                </option>
                            ))}
                        </SelectInput>
                        <TextInput
                            className="pr-6 pb-8 w-full lg:w-1/2"
                            label="Date & Time"
                            name="datetime"
                            type="datetime-local"
                            errors={errors.datetime}
                            value={data.datetime}
                            onChange={handleChange}
                        />
                        <TextInput
                            className="pr-6 pb-8 w-full lg:w-1/2"
                            label="Price"
                            name="price"
                            type="number"
                            errors={errors.price}
                            value={data.price}
                            onChange={handleChange}
                        />
                        <SelectInput
                            className="pr-6 pb-8 w-full lg:w-1/2"
                            label="Hall"
                            name="hall_id"
                            errors={errors.hall_id}
                            value={data.hall_id}
                            onChange={handleChange}
                        >
                            <option value="">Select a hall</option>
                            {halls.map((hall) => (
                                <option key={hall.id} value={hall.id}>
                                    {hall.name}
                                </option>
                            ))}
                        </SelectInput>
                    </div>
                    <div className="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
                        <LoadingButton
                            loading={processing}
                            type="submit"
                            className="btn-indigo ml-auto"
                        >
                            Create Show
                        </LoadingButton>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
