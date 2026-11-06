For the Toast Messages

$this->dispatch(
    'toast',
    title: 'Success',
    message: 'Your booking was submitted successfully.',
    type: 'success'
);

$this->dispatch(
    'toast',
    title: 'Something went wrong',
    message: 'Unable to process your request.',
    type: 'error'
);

$this->dispatch(
    'toast',
    title: 'Attention',
    message: 'Please check your booking details.',
    type: 'warning'
);

$this->dispatch(
    'toast',
    title: 'Information',
    message: 'Your booking is awaiting approval.',
    type: 'info'
);
