// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Mar-08
import React from 'react';
import { render, screen } from '@testing-library/react';
import ZambiaGridScheduler from './ZambiaGridScheduler';

test('renders learn react link', () => {
  render(<ZambiaGridScheduler />);
  const linkElement = screen.getByText(/learn react/i);
  expect(linkElement).toBeInTheDocument();
});
