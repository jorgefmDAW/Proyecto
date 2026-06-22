import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ReglasPuntuacion } from './reglas-puntuacion';

describe('ReglasPuntuacion', () => {
  let component: ReglasPuntuacion;
  let fixture: ComponentFixture<ReglasPuntuacion>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ReglasPuntuacion]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ReglasPuntuacion);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
